<?php

namespace App\Services\WMS;

use app\Libraries\Core;
use App\Models\WMS\DODetail;
use App\Models\WMS\DOHeader;
use App\Models\WMS\GetTransaction;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DOService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }

  //Get DOs
  public function index(Request $request)
  {
    DB::enableQueryLog();
    $query = new DOHeader;

    if ($request->input('start') && $request->input('end')) {
      $start = $request->input('start');
      $end = $request->input('end');

      $query = $query->whereBetween(DB::raw('do_date::date'), [$start, $end]);
    }

    if ($request->input('do_no')) {
      $query->where('uuid', $request->input('uuid'));
    }

    $query = $query->orderBy('do_date', 'asc')->get();

    // dd(DB::getQueryLog());
    // dd($query);

    return $this->core->setResponse('success', 'Get DOs', $query);
  }

  public function createDO($start, $end)
  {
    try {
      $userLogin = null;
      // Check Auth & update user uuid to deleted_by
      if (Auth::check()) {
        $user = Auth::user();
        $userLogin = $user->uuid;
      }

      DB::beginTransaction();
      // DB::enableQueryLog();
      $now = Carbon::now();

      $columnsToCheck = ['wms_do_date', 'wms_do_header_uuid'];
      $datas = GetTransaction::select(
        'uuid',
        'transaction_type',
        'transaction_date',
        DB::raw('to_char("transaction_date", \'YYYY-MM-DD\') AS trans_date'),
        'warehouse_uuid',
        'product_uuid',
        'product_attribute_uuid',
        'product_header_uuid',
        'name',
        'attribute_name',
        'description',
        'is_register',
        'weight',
        'qty_order',
        'qty_indent',
        'product_status',
        'stock_type'
      )->whereBetween(DB::raw('transaction_date::date'), [$start, $end])
        ->where(function ($query) use ($columnsToCheck) {
          foreach ($columnsToCheck as $column) {
            $query->orWhereNull($column);
          }
        })->lockForUpdate()->get();

      $newHeaders = [];
      $newDetails = [];
      $uuids = [];
      $uuidHeader = null;

      foreach ($datas as $data) {
        $keyHeader = $data['warehouse_uuid'];// . $data['sent_to'] . $data['to_uuid'];

        if (!isset($newHeaders[$keyHeader])) {
          $uuidHeader = Str::uuid();
          $newHeaders[$keyHeader] = [
            'uuid' => $uuidHeader,
            'do_date' => $now,
            'warehouse_uuid' => $data['warehouse_uuid'],
            'sent_to' => $data['sent_to'],
            'to_uuid' => $data['to_uuid'],
            // 'name' => $data['name'],
            // 'remarks' => $data['remarks'],
            // // 'notes' => $data['notes'],
            // 'description' => $data['description'],
            'transaction_uuids' => $data['uuid'],
            'total_transaction' => 0,
            'total_stock_in' => 0,
            'total_stock_out' => 0,
            'total_weight' => 0,
            'total_qty_order' => 0,
            'total_qty_sent' => 0,
            'total_qty_indent' => 0,
            'total_qty_remain' => 0,
            // 'stock_type' => $data['stock_type'],
            'created_at' => $now,
            'created_by' => $userLogin ? $userLogin : null,
            'updated_at' => $now,
            'updated_by' => $userLogin ? $userLogin : null,
          ];
        } else {
          GetTransaction::where('uuid', $data['uuid'])->update(['wms_do_date' => $now, 'wms_do_header_uuid' => $uuidHeader]);
          $newHeaders[$keyHeader]['transaction_uuids'] = $data['uuid'];
        }

        $newHeaders[$keyHeader]['total_transaction']++;
        $newHeaders[$keyHeader]['total_stock_out']  += $data['qty_order'] - $data['qty_indent'];;
        $newHeaders[$keyHeader]['total_weight'] += $data['weight'];
        $newHeaders[$keyHeader]['total_qty_order'] += $data['qty_order'];
        $newHeaders[$keyHeader]['total_qty_sent']  += $data['qty_order'] - $data['qty_indent'];
        $newHeaders[$keyHeader]['total_qty_indent'] += $data['qty_indent'];
        $newHeaders[$keyHeader]['total_qty_remain'] += $data['qty_indent'];


        // $keyDetail = $data['product_uuid'] . $data['product_attribute_uuid'] . $data['name'] .
        //   $data['warehouse_uuid'] . $data['sent_to'] . $data['to_uuid'];

        // if (!isset($newDetails[$keyDetail])) {
        //   GetTransaction::where('uuid', $data['uuid'])->update(['wms_do_date' => $now, 'wms_do_header_uuid' => $uuidHeader]);

        //   $newDetails[$keyDetail] = [
        //     'uuid' => Str::uuid(),
        //     'wms_do_header_uuid' => $uuidHeader,
        //     'product_uuid' => $data['product_uuid'],
        //     'product_attribute_uuid' => $data['product_attribute_uuid'],
        //     'name' => $data['name'],
        //     'attribute_name' => $data['attribute_name'],
        //     'description' => $data['description'],
        //     'is_register' => $data['is_register'],
        //     'product_status' => $data['product_status'],
        //     'weight' => 0,
        //     'stock_type' => $data['stock_type'],
        //     'qty_order' => 0,
        //     'qty_sent' => 0,
        //     'qty_indent' => 0,
        //     'qty_remain' => 0,
        //     'created_at' => $now,
        //     'created_by' => $userLogin ? $userLogin : null,
        //     'updated_at' => $now,
        //     'updated_by' => $userLogin ? $userLogin : null,
        //   ];
        // }else {
        //   $newDetails[$keyDetail]['wms_do_header_uuid'] = $uuidHeader;
        // }

        // $newDetails[$keyDetail]['weight'] += $data['weight'];
        // $newDetails[$keyDetail]['qty_order'] += $data['qty_order'];
        // $newDetails[$keyDetail]['qty_sent']  += $data['qty_order'] - $data['qty_indent'];
        // $newDetails[$keyDetail]['qty_indent'] = $data['qty_indent'];
        // $newDetails[$keyDetail]['qty_remain'] = $data['qty_indent'];
      }

      $newHeaders = array_values($newHeaders);
      DOHeader::insert($newHeaders);

      // $newDetails = array_values($newDetails);
      // DODetail::insert($newDetails);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Error during DO bulk create ' . $e->getMessage()], 500);
    }

    return $this->core->setResponse(
      'success',
      "Create DO from date $start to $end",
      count($newHeaders) . " record(s)",
      false,
      201
    );
  }

  // Create new DO
  public function store($request)
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

    return $this->createDO($request->start, $request->end);
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
