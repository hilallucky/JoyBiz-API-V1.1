<?php

namespace App\Repositories\WMS;

use app\Libraries\Core;
use App\Models\WMS\DODetail;
use App\Models\WMS\DOHeader;
use App\Models\WMS\GetTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DORepository
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
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
        })->orderByRaw('warehouse_uuid ASC, product_uuid, product_attribute_uuid, product_header_uuid, transaction_type, transaction_date')
        ->lockForUpdate()->get();

      $newHeaders = [];
      $uuidHeader = null;

      foreach ($datas as $data) {
        $keyHeader = $data['warehouse_uuid'] ? $data['warehouse_uuid'] : '--'; // . $data['sent_to'] . $data['to_uuid'];

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
          $newHeaders[$keyHeader]['transaction_uuids'] .= ',' . $data['uuid'];
        }

        $newHeaders[$keyHeader]['total_transaction']++;
        $newHeaders[$keyHeader]['total_stock_out']  += $data['qty_order'] - $data['qty_indent'];
        $newHeaders[$keyHeader]['total_weight'] += $data['weight'];
        $newHeaders[$keyHeader]['total_qty_order'] += $data['qty_order'];
        $newHeaders[$keyHeader]['total_qty_sent']  += $data['qty_order'] - $data['qty_indent'];
        $newHeaders[$keyHeader]['total_qty_indent'] += $data['qty_indent'];
        $newHeaders[$keyHeader]['total_qty_remain'] += $data['qty_indent'];
      }

      $newHeaders = array_values($newHeaders);
      DOHeader::insert($newHeaders);

      $this->doDetails($newHeaders, $userLogin);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Error during DO bulk create ' . $e->getMessage()], 500);
    }

    return $this->core->setResponse(
      'success',
      "Create DO from date $start to $end",
      (!empty($newHeaders) ? count($newHeaders) + 1 : 0) . " record(s)",
      false,
      201
    );
  }

  public function doDetails($headers, $user)
  {
    $user = $user ? $user : null;
    $now = Carbon::now();
    foreach ($headers as $header) {
      $do_uuid = $header['uuid'];

      $details = GetTransaction::selectRaw(
        "gen_random_uuid() as uuid, '$do_uuid' as wms_do_header_uuid,
      product_uuid, product_attribute_uuid, product_header_uuid, name,
      attribute_name, description, is_register, SUM(weight) as weight, stock_type,
      SUM(qty_order) as qty_order, SUM(qty_order) - SUM(qty_indent) as qty_sent, 
      SUM(qty_indent) as qty_indent, SUM(qty_indent) as qty_remain, product_status,
      '$now' as created_at, '$user' as created_by,
      '$now' as updated_at, '$user' as updated_by"
      )
        ->whereIn('uuid', explode(',', $header['transaction_uuids']))
        ->groupBy(
          'product_uuid',
          'product_attribute_uuid',
          'product_header_uuid',
          'name',
          'attribute_name',
          'description',
          'is_register',
          'stock_type',
          'product_status'
        )
        ->orderByRaw('product_uuid, product_attribute_uuid, product_header_uuid')->get();

      DODetail::insert($details->toArray());

      GetTransaction::whereIn('uuid', explode(',', $header['transaction_uuids']))
        ->lockForUpdate()->update([
          'wms_do_header_uuid' => $header['uuid'],
          'wms_do_date' => $header['do_date']
        ]);
    }
  }


  public function groupArray($data)
  {
      // $result = collect($array)->groupBy([
      //     'product_uuid',
      //     'product_attribute_uuid',
      //     'name',
      //     'warehouse_uuid',
      // ])->map(function ($group) {
      //     $firstItem = $group->first();

      //     return [
      //         'uuids' => $group->pluck('uuid')->implode(', '),
      //         'product_uuid' => $firstItem['product_uuid'] ?? null,
      //         'product_attribute_uuid' => $firstItem['product_attribute_uuid'] ?? null,
      //         'name' => $firstItem['name'] ?? null,
      //         'warehouse_uuid' => $firstItem['warehouse_uuid'] ?? null,
      //         'record' => count($group),
      //         'sum_total_qty' => $group->sum(function ($item) {
      //             return isset($item['total_qty']) ? $item['total_qty'] : 0;
      //         }),
      //         'sum_total_weight' => $group->sum(function ($item) {
      //             return isset($item['total_weight']) ? $item['total_weight'] : 0;
      //         }),
      //     ];
      // })->values()->toArray();

      // return $result;
      // // print_r($result);

      $summary = array_reduce($data, function ($carry, $item) {
        $productUuid = $item['product_uuid'];
        $attributeUuid = $item['product_attribute_uuid'];
    
        $groupKey = $productUuid . '-' . $attributeUuid;

        // Initialize the group if it doesn't exist
        if (!array_key_exists($groupKey, $carry)) {
            $carry[$groupKey] = [
                'total_qty' => 0,
                'total_weight' => 0,
                'total_qty_indent' => 0,
            ];
        }
    
        // Update the summarized data
        $carry[$groupKey]['product_attribute_uuid'] = $item['product_attribute_uuid'];
        $carry[$groupKey]['warehouse_uuid'] = $item['warehouse_uuid'];
        $carry[$groupKey]['name'] = $item['name'];
        $carry[$groupKey]['total_qty'] += $item['total_qty'];
        $carry[$groupKey]['total_weight'] += $item['total_weight'];
        $carry[$groupKey]['total_qty_indent'] += $item['total_qty_indent'];
    
        return $carry;
    }, []);
    
    // Print the summarized data
      return array_values($summary);
  }
}
