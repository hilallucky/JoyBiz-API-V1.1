<?php

namespace App\Services\WMS;

use App\Http\Resources\Warehouses\WarehouseResource;
use App\Http\Resources\WMS\GetTransactionResource;
use app\Libraries\Core;
use App\Models\Orders\Production\OrderHeader;
use App\Models\WMS\GetTransaction;
use App\Models\WMS\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GetTransactionService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }

  //Get all transactions
  public function index(Request $request)
  {
    // DB::enableQueryLog();

    $query = GetTransaction::query();

    // Apply filters based on request parameters
    if ($request->input('start') && $request->input('end')) {
      $start = $request->input('start');
      $end = $request->input('end');

      $query = $query->whereBetween(DB::raw('get_date::date'), [$start, $end]);
    }

    if ($request->input('wh_name')) {
      $whName = $request->input('wh_name');
    }

    $query = $query->with(
      'doHeader',
      ['warehouse' => function ($query) use ($whName) {
        return $query->where('name', 'ilike', '%' . $whName . '%');
      }]
    )->orderBy('get_date', 'asc')->get();

    // $query = DB::getQueryLog();
    // dd($query);

    $queryList = GetTransactionResource::collection($query);

    return $this->core->setResponse('success', 'Transactions', $queryList);
  }

  // Get new Transaction
  public function store(Request $request)
  {
    $validator = $this->validation('create', $request);

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
      DB::beginTransaction();

      $start = $request->input('start');
      $end = $request->input('end');
      // get only paid and not yet processed by warehouse
      $orders = OrderHeader::with('details.productPrice.product')
        ->whereBetween(DB::raw('transaction_date::date'), [$start, $end])
        ->whereIn('status', ['1'])->lockForUpdate()->get();

      $newDatas = [];
      $getDate = Carbon::now();
      $stockOut = 0;
      foreach ($orders as $order) {
        $newDatas[] = [
          'uuid' => Str::uuid(),
          'get_date' => $order->uuid,
          'transaction_type' => '1',
          'transaction_date' => $order->transaction_date,
          'transaction_header_uuid' => $order->uuid,
          'transaction_detail_uuid' => $order->details->uuid,
        ];

        foreach ($orders->details as $detail) {
          $stockOut = $detail->product_price->product->status == '1' ? $detail->qty : 0;
          $indent = $detail->product_price->product->status == '4' ? $detail->qty : 0;
          $newDatas[] = [
            'product_uuid' => $detail->product_uuid,
            // 'product_attribute_uuid' => $detail->product_attribute_uuid,
            // 'product_header_uuid' => $detail->product_header_uuid,
            'name' => $detail->name,
            'attribute_name' => $detail->attribute_name,
            'description' => $detail->description,
            'is_register' => $detail->is_register,
            'weight' => $detail->weight,
            'stock_in' => 0,
            'stock_out' => $detail->qty,
            'qty' => $detail->qty,
            'qty_indent' => $detail->qty_indent,
          ];
        }
      }

      // $warehouseList = Warehouse::whereIn(
      //   'uuid',
      //   $newWarehouses
      // )->get();

      $orderList = $orders; //WarehouseResource::collection($warehouseList);

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
      'Order created',
      $orderList,
      false,
      201
    );
  }

  // //Get Warehouse by ids
  // public function show(Request $request, $uuid)
  // {
  //   if (!Str::isUuid($uuid)) {
  //     return $this->core->setResponse(
  //       'error',
  //       'Invalid UUID format',
  //       NULL,
  //       FALSE,
  //       400
  //     );
  //   }

  //   $status = $request->input('status', "1");

  //   $warehouse = Warehouse::where(['uuid' => $uuid, 'status' => $status])->get();

  //   if (!isset($warehouse)) {
  //     return $this->core->setResponse(
  //       'error',
  //       'Warehouse Not Found',
  //       NULL,
  //       FALSE,
  //       400
  //     );
  //   }

  //   $warehouseList = WarehouseResource::collection($warehouse);

  //   return $this->core->setResponse('success', 'Warehouse Found', $warehouseList);
  // }

  // //UpdateBulk Warehouse
  // public function updateBulk(Request $request)
  // {
  //   $warehouses = $request->all();

  //   $validator = $this->validation('update', $request);

  //   if ($validator->fails()) {
  //     return $this->core->setResponse(
  //       'error',
  //       $validator->messages()->first(),
  //       NULL,
  //       false,
  //       422
  //     );
  //   }

  //   $status = "1";

  //   try {
  //     DB::beginTransaction();

  //     // Check Auth & update user uuid to deleted_by
  //     if (Auth::check()) {
  //       $user = Auth::user();
  //     }

  //     foreach ($warehouses as $warehouseData) {
  //       if (isset($warehouseData['status'])) {
  //         $status = $warehouseData['status'];
  //       }

  //       $warehouse = Warehouse::lockForUpdate()
  //         ->where('uuid', $warehouseData['uuid'])->firstOrFail();

  //       $warehouse->update([
  //         'name' => $warehouseData['name'],
  //         'phone' => $warehouseData['phone'],
  //         'mobile_phone' => $warehouseData['mobile_phone'],
  //         'email' => $warehouseData['email'],
  //         'province' => $warehouseData['province'],
  //         'city' => $warehouseData['city'],
  //         'district' => $warehouseData['district'],
  //         'village' => $warehouseData['village'],
  //         'zip_code' => $warehouseData['zip_code'],
  //         'details' => $warehouseData['details'],
  //         'description' => $warehouseData['description'],
  //         'remarks' => $warehouseData['remarks'],
  //         'status' => $status,
  //         // 'updated_by' => $user->uuid,
  //       ]);

  //       $updatedCountries[] = $warehouse->toArray();
  //     }

  //     $warehouseList = Warehouse::whereIn(
  //       'uuid',
  //       array_column($updatedCountries, 'uuid')
  //     )->get();

  //     $warehouseList = WarehouseResource::collection($warehouseList);

  //     DB::commit();
  //   } catch (QueryException $e) {
  //     DB::rollback();
  //     return $this->core->setResponse(
  //       'error',
  //       'Warehouse fail to updated. ' . $e->getMessage(),
  //       NULL,
  //       FALSE,
  //       500
  //     );
  //   } catch (\Exception $ex) {
  //     DB::rollback();
  //     return $this->core->setResponse(
  //       'error',
  //       "Warehouse fail to updated. " . $ex->getMessage(),
  //       NULL,
  //       FALSE,
  //       500
  //     );
  //   }

  //   return $this->core->setResponse(
  //     'success',
  //     'Warehouse updated',
  //     $warehouseList
  //   );
  // }

  // //Delete Warehouse by ids
  // public function destroyBulk(Request $request)
  // {

  //   $validator = $this->validation(
  //     'delete',
  //     $request
  //   );

  //   if ($validator->fails()) {
  //     return $this->core->setResponse(
  //       'error',
  //       $validator->messages()->first(),
  //       NULL,
  //       false,
  //       422
  //     );
  //   }

  //   $uuids = $request->input('uuids');
  //   $warehouses = null;
  //   try {
  //     $warehouses = Warehouse::lockForUpdate()
  //       ->whereIn(
  //         'uuid',
  //         $uuids
  //       );

  //     // Compare the count of found UUIDs with the count from the request array
  //     if (
  //       !$warehouses ||
  //       (count($warehouses->get()) !== count($uuids))
  //     ) {
  //       return response()->json(
  //         ['message' => 'Warehouses fail to deleted, because invalid uuid(s)'],
  //         400
  //       );
  //     }

  //     //Check Auth & update user uuid to deleted_by
  //     // if (Auth::check()) {
  //     //     $user = Auth::user();
  //     // $warehouses->deleted_by = $user->uuid;
  //     // $warehouses->save();
  //     // }

  //     $warehouses->delete();
  //   } catch (\Exception $e) {
  //     return response()->json(
  //       ['message' => 'Error during bulk deletion ' . $e->getMessage()],
  //       500
  //     );
  //   }

  //   return $this->core->setResponse(
  //     'success',
  //     "Warehouses deleted",
  //     null,
  //     200
  //   );
  // }

  private function validation($type = null, $request)
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
