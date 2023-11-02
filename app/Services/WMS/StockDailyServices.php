<?php

namespace App\Services\WMS;

use app\Libraries\Core;
use App\Models\Orders\Production\OrderHeader;
use App\Models\WMS\GetTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StockDailyServicesService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
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

  //   return $this->core->setResponse('success', 'Get order transactions', $query);
  // }

  // Get new Transaction
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

    $userLogin = null;
    // Check Auth & update user uuid to deleted_by
    if (Auth::check()) {
      $user = Auth::user();
      $userLogin = $user->uuid;
    }

    try {
      DB::enableQueryLog();
      DB::beginTransaction();

      $start = $request->input('start');
      $end = $request->input('end');
      // get only paid and not yet processed by warehouse
      $orders = OrderHeader::with('details.productPrice.product.attributes')
        ->whereBetween(DB::raw('transaction_date::date'), [$start, $end])
        ->whereIn('status', ['1'])->where('date_transfered_to_wms', null)->lockForUpdate()->get();

      $newDatas = [];
      $getDate = Carbon::now();
      $stockOut = 0;
      $headerNo = 0;
      $detailNo = 0;
      foreach ($orders as $order) {
        $headerNo++;
        foreach ($order->details as $detail) {
          $newData = [
            'uuid' => Str::uuid(),
            'get_date' => $getDate,
            'transaction_type' => '1',
            'transaction_date' => $order->transaction_date,
            'transaction_header_uuid' => $order->uuid,
            'transaction_detail_uuid' => $detail->uuid,
            'warehouse_uuid' => null,
            'stock_in' => 0,
            'stock_type' => '2',
            'created_at' => $getDate,
            'created_by' => null,
            'updated_at' => $getDate,
            'updated_by' => null,
          ];

          array_push($newDatas, $newData);

          if ($detail->is_product_group == '1') {
            $groupProducts = $detail->group($detail->product_uuid, $detail->qty);
            $groupNo = 0;
            foreach ($groupProducts as $groupProduct) {
              if ($groupNo > 0) {
                $newData['uuid'] = Str::uuid();
                array_push($newDatas, $newData);
              }

              $stockOut = $groupProduct->status == '1' ? $groupProduct->qty : 0;
              $indent = $groupProduct->status == '4' ? $groupProduct->qty : 0;
              $weight = number_format((float)$groupProduct->weight, 2, '.', '') * $stockOut;

              $newDatas[$detailNo]['product_uuid'] = $groupProduct->product_uuid;
              $newDatas[$detailNo]['product_attribute_uuid'] = null;
              $newDatas[$detailNo]['product_header_uuid'] = $groupProduct->uuid;
              $newDatas[$detailNo]['name'] = $groupProduct->name;
              $newDatas[$detailNo]['attribute_name'] = null; //$groupProduct->productPrice->product;
              $newDatas[$detailNo]['description'] = $groupProduct->description;
              $newDatas[$detailNo]['is_register'] = $groupProduct->is_register;
              $newDatas[$detailNo]['weight'] = number_format((float)$groupProduct->weight, 2, '.', '');
              $newDatas[$detailNo]['sub_weight'] = $weight;
              // $newDatas[$detailNo]['stock_in'] = 0;
              $newDatas[$detailNo]['stock_out'] = (int)$stockOut;
              $newDatas[$detailNo]['qty_order'] = (int)$groupProduct->qty;
              $newDatas[$detailNo]['qty_indent'] = (int)$indent;
              $newDatas[$detailNo]['product_status'] = $groupProduct->status;
              $detailNo++;
              $groupNo++;
            }
          } else if ($detail->is_product_group != '1') {
            $stockOut = $detail->productPrice->product->status == '1' ? $detail->qty : 0;
            $indent = $detail->productPrice->product->status == '4' ? $detail->qty : 0;
            $weight = number_format((float)$detail->productPrice->product->weight, 2, '.', '') * $stockOut;

            $newDatas[$detailNo]['product_uuid'] = $detail->uuid;
            $newDatas[$detailNo]['product_attribute_uuid'] = $detail->uuid;
            $newDatas[$detailNo]['product_header_uuid'] = $detail->uuid;
            $newDatas[$detailNo]['name'] = $detail->productPrice->product->name;
            $newDatas[$detailNo]['attribute_name'] = null; //$detail->productPrice->product;
            $newDatas[$detailNo]['description'] = $detail->productPrice->product->description;
            $newDatas[$detailNo]['is_register'] = $detail->productPrice->product->is_register;
            $newDatas[$detailNo]['weight'] = number_format((float)$detail->productPrice->product->weight, 2, '.', '');
              $newDatas[$detailNo]['sub_weight'] = $weight;
            // $newDatas[$detailNo]['stock_in'] = 0;
            $newDatas[$detailNo]['stock_out'] = (int)$stockOut;
            $newDatas[$detailNo]['qty_order'] = (int)$detail->qty;
            $newDatas[$detailNo]['qty_indent'] = (int)$indent;
            $newDatas[$detailNo]['product_status'] = $detail->status;
            $detailNo++;
          }
        }
        $order->transfered_to_wms_by = null;
        $order->date_transfered_to_wms = $getDate;
        $order->save();
      }

      $result = array_reduce($newDatas, function($carry, $item){
        if(!isset($carry[$item['product_uuid']])){
            $carry[$item['product_uuid']] = ['product_uuid'=>$item['product_uuid'],'qty_order'=>$item['qty_order']]; 
        } else {
            $carry[$item['product_uuid']]['qty_order'] += $item['qty_order'];
        }
        return $carry;
      });
      // return $result;
      
      GetTransaction::insert($newDatas);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        'Warehouse fail to created. ' . $e->getMessage(),
        NULL,
        FALSE,
        500
      );
    }

    return $this->core->setResponse(
      'success',
      "Get transaction from date $start to $end",
      $orders->count() . " record(s)",
      false,
      201
    );
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
