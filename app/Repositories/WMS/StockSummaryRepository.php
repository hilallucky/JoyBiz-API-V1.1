<?php

namespace App\Repositories\WMS;

use app\Libraries\Core;
use App\Models\WMS\DODetail;
use App\Models\WMS\DOHeader;
use App\Models\WMS\GetTransaction;
use App\Models\WMS\StockPeriod;
use App\Models\WMS\StockSummaryHeader;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockSummaryRepository
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }

  public function checkPeriod($start, $end)
  {
    $startDate = Carbon::parse($start);
    $endDate = Carbon::parse($end);
    $startDay = $startDate->dayName;
    $endDay = $endDate->dayName;

    $periodId = null;
    $period = StockPeriod::where('start_date', '>=', $startDate)->where('end_date', '<=', $endDate)->first();

    if (empty($period)) {
      $stockPeriod = StockPeriod::create([
        'stock_period' => 'daily',
        'start_date' => $startDate->format('Y-m-d'),
        'end_date' => $endDate->format('Y-m-d'),
        'start_day_name' => $startDay,
        'end_day_name' => $endDay
      ]);
      $periodId = $stockPeriod->id;
    } else {
      $periodId = $period->id;
    }

    return $periodId;
  }

  public function checkStock($periodId, $data, $userLogin)
  {
    $check = StockSummaryHeader::where('product_uuid', $data->product_uuid)
      ->where('product_attribute_uuid', $data->product_attribute_uuid)
      ->where('product_header_uuid', $data->product_header_uuid)
      ->whereRaw("stock_date::date = '" . Carbon::now()->format('Y-m-d') . "'")
      // ->where('warehouse_uuid', $data->warehouse_uuid)
      ->orderBy('id', 'desc')
      ->first();

    if (!$check) {
      StockSummaryHeader::create(
        [
          'uuid' => Str::uuid(),
          'stock_process_uuid' => $periodId,
          'warehouse_uuid' => null,
          'stock_date' => Carbon::now(),
          'product_uuid' => $data->product_uuid,
          'product_attribute_uuid' => $data->product_attribute_uuid,
          'product_header_uuid' => $data->product_header_uuid,
          'name' => $data->name,
          'attribute_name' => $data->attribute_name,
          'description' => $data->description,
          'is_register' => $data->is_register,
          'weight' => $data->weight,
          'stock_in' => 0,
          'stock_out' => $data->qty_order,
          'stock_previous' => 0,
          'stock_current' => 0 - ($data->qty_order - $data->qty_indent),
          'stock_to_sale' => $data->qty_order * 100,
          'indent' => $data->qty_indent,
          'stock_type' => $data->stock_type,
          'created_by' => $userLogin,
          'updated_by' => $userLogin
        ]
      );
    } else {
      $check->stock_out += $data->qty_order;
      $check->stock_previous = $check->stock_current;
      $check->stock_current -= ($data->qty_order - $data->qty_indent);
      $check->stock_to_sale = ($data->qty_order - $data->qty_indent) * 10;
      $check->indent += $data->qty_indent;
      $check->save();
    }
  }

  public function createStock($start, $end)
  {
    try {
      $userLogin = null;
      // Check Auth & update user uuid to deleted_by
      if (Auth::check()) {
        $user = Auth::user();
        $userLogin = $user->uuid;
      }

      DB::beginTransaction();
      DB::enableQueryLog();

      $datas = DB::table('wms_do_headers')
        ->join('wms_do_details', 'wms_do_headers.uuid', '=', 'wms_do_details.wms_do_header_uuid')
        ->selectRaw(
          "wms_do_details.product_uuid, wms_do_details.product_attribute_uuid, wms_do_details.product_header_uuid,
          wms_do_details.name, wms_do_details.attribute_name, wms_do_details.description, wms_do_details.is_register,
          wms_do_details.product_status, wms_do_details.weight, wms_do_details.stock_type,
          SUM(wms_do_details.qty_order) AS qty_order, SUM(wms_do_details.qty_sent) AS qty_sent,
          SUM(wms_do_details.qty_indent) AS qty_indent, SUM(wms_do_details.qty_remain) AS qty_remain,
          array_to_string(array_agg(wms_do_headers.uuid), ',') as \"uuids\""
        )
        ->where(function ($q) {
          $q->orWhere('wms_do_headers.daily_stock', null)
            ->orWhere('wms_do_headers.daily_stock', 0);
        })
        ->whereBetween('wms_do_headers.do_date', [$start, $end])
        ->groupBy(
          'wms_do_details.product_uuid',
          'wms_do_details.product_attribute_uuid',
          'wms_do_details.product_header_uuid',
          'wms_do_details.name',
          'wms_do_details.attribute_name',
          'wms_do_details.description',
          'wms_do_details.is_register',
          'wms_do_details.product_status',
          'wms_do_details.weight',
          'wms_do_details.stock_type',
        )->get();

      $periodId = $this->checkPeriod($start, $end);

      foreach ($datas as $data) {
        $this->checkStock($periodId, $data, $userLogin);
      }

      if (count($datas) > 0) {
        DOHeader::whereIn('uuid', array_unique(explode(',', $datas->pluck('uuids')->implode(','))))->update([
          'daily_stock' => 1,
          'daily_stock_date' => Carbon::now()
        ]);
      }
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Error during Stock create ' . $e->getMessage()], 500);
    }

    return $this->core->setResponse(
      'success',
      "Create Stock from date $start to $end",
      (count($datas) > 0 ? count($datas) + 1 : 0) . " record(s)",
      false,
      201
    );
  }

  public function updateStockSale($productUuid, $warehouseUuid, $qty, $effect)
  {
    $stock = StockSummaryHeader::where('product_uuid', $productUuid)
      ->where('warehouse_uuid', $warehouseUuid)
      ->orderBy('id', 'desc')
      ->first()->lockForUpdate();

    if ($effect == '-') {
      $stock->stock_to_sale -= $qty;
    } elseif ($effect == '+') {
      $stock->stock_to_sale += $qty;
    }
    $stock->save();
  }

  public function groupArray($data)
  {
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
