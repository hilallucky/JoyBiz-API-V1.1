<?php

namespace App\Services\WMS;

use app\Libraries\Core;
use App\Models\Orders\Production\OrderHeader;
use App\Models\WMS\GetTransaction;
use App\Models\WMS\StockProcesses;
use App\Models\WMS\StockSummaryHeader;
use App\Repositories\WMS\StockSummaryRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StockDailyService
{
  private StockSummaryRepository $ssRepo;
  public $core;

  public function __construct(StockSummaryRepository $ssRepo)
  {
    $this->core = new Core();
    $this->ssRepo = $ssRepo;
  }

  //Get all transactions
  // public function index(Request $request)
  // {
  //   // DB::enableQueryLog();

  //   $query = new GetTransaction;
  //   // Apply filters based on request parameters'transaction_header_uuid',

  //   if ($request->input('start') && $request->input('end')) {
  //     $start = $request->input('start');
  //     $end = $request->input('end');

  //     $query = $query->whereBetween(DB::raw('get_date::date'), [$start, $end]);
  //   }

  //   if ($request->input('do_no')) {
  //     $query->where('wms_do_header_uuid', $request->input('do_no'));
  //   }

  //   $query = $query //->with('doHeader')
  //     ->select(DB::raw("deleted_at, get_date, transaction_date, transaction_header_uuid, " .
  //       "product_uuid, product_attribute_uuid, product_header_uuid, name, attribute_name, description, " .
  //       "is_register, sum(weight) as weight, sum(sub_weight) as sub_weight, sum(stock_in) as stock_in, ".
  //       "sum(stock_out) as stock_out, sum(qty_order) as qty_order, sum(qty_indent) as qty_indent, ".
  //       "product_status, stock_type"))
  //     ->groupBy('deleted_at', 'get_date', 'transaction_date', 'transaction_header_uuid', 'product_uuid',
  //       'product_attribute_uuid', 'product_header_uuid', 'name', 'attribute_name', 'description',
  //       'is_register', 'product_status', 'stock_type')
  //     ->orderBy('get_date', 'asc')
  //     ->orderBy('transaction_date', 'asc')
  //     ->get();

  //   $query->setVisible([
  //     'get_date', 'transaction_date', 'transaction_header_uuid', 'product_uuid', 'product_attribute_uuid',
  //     'product_header_uuid', 'name', 'attribute_name', 'description', 'is_register', 'weight', 'sub_weight',
  //     'stock_in', 'stock_out', 'qty_order', 'qty_indent','product_status', 'stock_type']);


  // $query = DB::getQueryLog();
  // dd($query);

  // dd($data);

  //   return $this->core->setResponse('success', 'Get order transactions', $query);
  // }

  public function index(Request $request)
  {
    return $this->getData($request->start, $request->end, $request->type);
  }

  public function stockProcess($processedDate, $userUuid)
  {
    return StockProcesses::create([
      'uuid' => Str::uuid(),
      'processed_date' => $processedDate,
      'processed_by_uuid' => $userUuid,
      'created_by' => $userUuid,
      'updated_by' => $userUuid,
    ]);
  }

  public function getData($start, $end, $periodType = 'daily_stock')
  {
    try {
      DB::beginTransaction();
      DB::enableQueryLog();
      $now = Carbon::now();

      $datas = GetTransaction::whereBetween(DB::raw('wms_do_date::date'), [$start, $end])
        ->whereNull('wms_do_header_uuid')->get();
      // return $datas;

      $process = $this->stockProcess($now, null);
      $newData = [];

      foreach ($datas as $data) {
        $index = array_search($data['product_uuid'], array_column($newData, 'product_uuid'));
        // print_r('hellooooo = ' . $index);
        // $prevStock = StockSummaryHeader::where('product_uuid', $data->product_uuid)
        //   ->orderBy('created_at', 'desc')->first();

        if ($index !== false) {
          $newData[$index]['stock_out'] += $data->stock_out;
        } else {
          $newData[] = [
            'uuid' => Str::uuid(),
            'stock_process_uuid' => $process->uuid,
            'warehouse_uuid' => null,
            'stock_date' => $now,
            'product_uuid' => $data->product_uuid,
            'product_attribute_uuid' => $data->product_attribute_uuid,
            'product_header_uuid' => $data->product_header_uuid,
            'name' => $data->name,
            'attribute_name' => $data->attribute_name,
            'description' => $data->description,
            'is_register' => $data->is_register,
            'weight' => $data->weight,
            // 'stock_in'=>$data->stock_in,
            'stock_out' => $data->stock_out,
            'stock_previous' => $data->stock_previous,
            'stock_current' => $data->stock_current,
            'stock_to_sale' => $data->stock_to_sale,
            'indent' => $data->indent,
            'stock_type' => $data->stock_type,
            // 'created_by'=>$data->,
            // 'updated_by'=>$data->,
          ];
        }
      }

      $newStock = StockSummaryHeader::insert($newData);

      // $prevStock = StockSummaryHeader::where('product_uuid', $data->product_uuid)
      // ->orderBy('created_at', 'desc')->first();

      DB::commit();
      return $newStock;
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(
        ['message' => 'Error during bulk create ' . $e->getMessage()],
        500
      );
    }
  }

  // Create new Period
  public function store(Request $request)
  {
    $validator = $this->validation($request, 'create');

    if ($validator->fails()) {
      return $this->core->setResponse(
        'error',
        $validator->messages()->first(),
        null,
        false,
        422
      );
    }

    return $this->ssRepo->createStock($request->start, $request->end);
  }

  //Delete Warehouse by ids
  public function destroyBulk(Request $request)
  {
    $validator = $this->validation('delete', $request);

    if ($validator->fails()) {
      return $this->core->setResponse(
        'error',
        $validator->messages()->first(),
        NULL,
        false,
        422
      );
    }

    $uuids = $request->input('uuids');
    $transactions = null;
    try {
      $transactions = GetTransaction::lockForUpdate()->whereIn('uuid', $uuids);

      // Compare the count of found UUIDs with the count from the request array
      if (!$transactions || (count($transactions->get()) !== count($uuids))) {
        return response()->json(['message' => 'Warehouses fail to deleted, because invalid uuid(s)'], 400);
      }

      // Check Auth & update user uuid to deleted_by
      if (Auth::check()) {
        $user = Auth::user();
        $transactions->deleted_by = $user->uuid;
        $transactions->save();
      }

      $transactions->delete();
    } catch (\Exception $e) {
      return response()->json(['message' => 'Error during bulk deletion ' . $e->getMessage()], 500);
    }

    return $this->core->setResponse(
      'success',
      "Warehouses data get transaction deleted",
      null,
      200
    );
  }

  private function validation($request, $type = null)
  {
    switch ($type) {
      case 'delete':
        $validator = [
          'uuids' => 'required|array',
          'uuids.*' => 'required|uuid',
        ];
        break;
      case 'create' || 'update':
        $validator = [
          'start' => 'required|date_format:Y-m-d',
          'end' => 'required|date_format:Y-m-d',
        ];
        break;
      default:
        $validator = [];
    }

    return Validator::make($request->all(), $validator);
  }
}
