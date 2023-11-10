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

  public function groupArray()
  {
    $data = [
      [
        "product_uuid" => "082efd04-4d49-11ee-9bbe-01601eb00f7f",
        "product_attribute_uuid" => null,
        "uuid" => "8b3da845-e54c-47e4-b808-dcf7857305bf",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "6ffb84f4-471f-4070-8aec-23e372de6c60",
        "name" => "Runaway",
        "remarks" => null,
        "notes" => null,
        "description" => "Runaway Desc",
        "total_weight" => 0.8,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 26,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => null,
        "uuid" => "121ce299-c606-4bd4-9be4-e38d60e9d39f",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "759d48a4-67c4-492f-9689-c07b2a42c721",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.8,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 4,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "6ee02ee2-1d85-4c80-8a56-907ec1175ac5",
        "uuid" => "ed2e9aac-21a0-4e29-bb9a-fe990f78a810",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "8fe0eff8-9598-448c-83d1-c8407a458138",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.4,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 2,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "6ee02ee2-1d85-4c80-8a56-907ec1175ac5",
        "uuid" => "cf13e328-28c1-464d-abd7-062373c64bax",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "5661df20-d02f-45b5-8d48-137049545d2e",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 4.0,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 20,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "6ee02ee2-1d85-4c80-8a56-907ec1175ac5",
        "uuid" => "cf13e328-28c1-464d-abd7-062373c64baf",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "5661df20-d02f-45b5-8d48-137049545d2e",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.4,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 2,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "458e5502-f0c7-46d1-83ac-a3671605a2e2",
        "uuid" => "da4dbddf-727b-459d-a06b-11c780d227bc",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "f911f19c-616e-4333-af7f-51fddd0e7faa",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.4,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 2,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "2f6a6bf3-ad7d-47c4-a74e-113cb40a7760",
        "uuid" => "e144581f-1865-4eaf-9a78-3d96cc43b647",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "6a2d52a4-cba1-44b9-9388-cd839986ad74",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.4,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 2,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "6ee02ee2-1d85-4c80-8a56-907ec1175ac5",
        "uuid" => "c38d75a5-70bc-43ff-95a8-d930265cd078",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "41c60bf8-290e-46fa-82f1-6d3708689118",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.4,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 2,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ]
    ];

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
