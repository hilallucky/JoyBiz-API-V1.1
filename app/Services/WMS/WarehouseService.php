<?php

namespace App\Services\WMS;

use App\Http\Resources\WMS\WarehouseResource;
use app\Libraries\Core;
use App\Models\WMS\Warehouse;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WarehouseService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }

  //Get all warehouse
  public function index(Request $request)
  {
    // DB::enableQueryLog();

    $query = Warehouse::query();

    // Apply filters based on request parameters
    if ($request->has('status')) {
      $query->where('status', $request->input('status'));
    } else {
      $query->where('status', "1");
    }

    if ($request->has('name')) {
      $param = $request->input('name');

      $query = $query->where(
        function ($q) use ($param) {
          $q->orWhere('code', 'ilike', '%' . $param . '%')
            ->orWhere('name', 'ilike', '%' . $param . '%')
            ->orWhere('phone', 'ilike', '%' . $param . '%')
            ->orWhere('mobile_phone', 'ilike', '%' . $param . '%')
            ->orWhere('email', 'ilike', '%' . $param . '%')
            ->orWhere('province', 'ilike', '%' . $param . '%')
            ->orWhere('city', 'ilike', '%' . $param . '%')
            ->orWhere('district', 'ilike', '%' . $param . '%')
            ->orWhere('village', 'ilike', '%' . $param . '%')
            ->orWhere('details', 'ilike', '%' . $param . '%')
            ->orWhere('remarks', 'ilike', '%' . $param . '%')
            ->orWhere('notes', 'ilike', '%' . $param . '%')
            ->orWhere('description', 'ilike', '%' . $param . '%');
        }
      );
    }

    $warehouses = $query->get()->take(100);

    // $query = DB::getQueryLog();
    // dd($query);

    $warehouseList = WarehouseResource::collection($warehouses);

    return $this->core->setResponse(
      'success',
      'Warehouse Founded',
      $warehouseList
    );
  }

  //Create new Warehouse
  public function store(Request $request)
  {
    $validator = $this->validation(
      'create',
      $request
    );

    if ($validator->fails()) {
      return $this->core->setResponse(
        'error',
        $validator->messages()->first(),
        null,
        false,
        422
      );
    }

    $status = "1";

    try {
      DB::beginTransaction();

      // Check Auth & update user uuid to deleted_by
      if (Auth::check()) {
        $user = Auth::user();
      }

      $warehouses = $request->all();
      foreach ($warehouses as $warehouse) {
        if (isset($warehouse['status'])) {
          $status = $warehouse['status'];
        }

        $newWarehouse = [
          'uuid' => Str::uuid(),
          'code' => $warehouse['code'],
          'name' => $warehouse['name'],
          'phone' => $warehouse['phone'],
          'mobile_phone' => $warehouse['mobile_phone'],
          'email' => $warehouse['email'],
          'province' => $warehouse['province'],
          'city' => $warehouse['city'],
          'district' => $warehouse['district'],
          'village' => $warehouse['village'],
          'zip_code' => $warehouse['zip_code'],
          'details' => $warehouse['details'],
          'description' => $warehouse['description'],
          'remarks' => $warehouse['remarks'],
          'status' => $status,
          // 'created_by' => $user->uuid,
        ];

        $newWarehouseAdd = new Warehouse($newWarehouse);
        $newWarehouseAdd->save();

        $newWarehouses[] = $newWarehouseAdd->uuid;
      }

      $warehouseList = Warehouse::whereIn(
        'uuid',
        $newWarehouses
      )->get();

      $warehouseList = WarehouseResource::collection($warehouseList);

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
      'Warehouse created',
      $warehouseList,
      false,
      201
    );
  }

  //Get Warehouse by ids
  public function show(Request $request, $uuid)
  {
    if (!Str::isUuid($uuid)) {
      return $this->core->setResponse(
        'error',
        'Invalid UUID format',
        NULL,
        FALSE,
        400
      );
    }

    $status = $request->input('status', "1");

    $warehouse = Warehouse::where(['uuid' => $uuid, 'status' => $status])->get();

    if (!isset($warehouse)) {
      return $this->core->setResponse(
        'error',
        'Warehouse Not Found',
        NULL,
        FALSE,
        400
      );
    }

    $warehouseList = WarehouseResource::collection($warehouse);

    return $this->core->setResponse('success', 'Warehouse Found', $warehouseList);
  }

  //UpdateBulk Warehouse
  public function updateBulk(Request $request)
  {
    $warehouses = $request->all();

    $validator = $this->validation('update', $request);

    if ($validator->fails()) {
      return $this->core->setResponse(
        'error',
        $validator->messages()->first(),
        NULL,
        false,
        422
      );
    }

    $status = "1";

    try {
      DB::beginTransaction();

      // Check Auth & update user uuid to deleted_by
      if (Auth::check()) {
        $user = Auth::user();
      }

      foreach ($warehouses as $warehouseData) {
        if (isset($warehouseData['status'])) {
          $status = $warehouseData['status'];
        }

        $warehouse = Warehouse::lockForUpdate()
          ->where('uuid', $warehouseData['uuid'])->firstOrFail();

        $warehouse->update([
          'name' => $warehouseData['name'],
          'phone' => $warehouseData['phone'],
          'mobile_phone' => $warehouseData['mobile_phone'],
          'email' => $warehouseData['email'],
          'province' => $warehouseData['province'],
          'city' => $warehouseData['city'],
          'district' => $warehouseData['district'],
          'village' => $warehouseData['village'],
          'zip_code' => $warehouseData['zip_code'],
          'details' => $warehouseData['details'],
          'description' => $warehouseData['description'],
          'remarks' => $warehouseData['remarks'],
          'status' => $status,
          // 'updated_by' => $user->uuid,
        ]);

        $updatedCountries[] = $warehouse->toArray();
      }

      $warehouseList = Warehouse::whereIn(
        'uuid',
        array_column($updatedCountries, 'uuid')
      )->get();

      $warehouseList = WarehouseResource::collection($warehouseList);

      DB::commit();
    } catch (QueryException $e) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        'Warehouse fail to updated. ' . $e->getMessage(),
        NULL,
        FALSE,
        500
      );
    } catch (\Exception $ex) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        "Warehouse fail to updated. " . $ex->getMessage(),
        NULL,
        FALSE,
        500
      );
    }

    return $this->core->setResponse(
      'success',
      'Warehouse updated',
      $warehouseList
    );
  }

  //Delete Warehouse by ids
  public function destroyBulk(Request $request)
  {

    $validator = $this->validation(
      'delete',
      $request
    );

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
    $warehouses = null;
    try {
      $warehouses = Warehouse::lockForUpdate()
        ->whereIn(
          'uuid',
          $uuids
        );

      // Compare the count of found UUIDs with the count from the request array
      if (
        !$warehouses ||
        (count($warehouses->get()) !== count($uuids))
      ) {
        return response()->json(
          ['message' => 'Warehouses fail to deleted, because invalid uuid(s)'],
          400
        );
      }

      //Check Auth & update user uuid to deleted_by
      // if (Auth::check()) {
      //     $user = Auth::user();
      // $warehouses->deleted_by = $user->uuid;
      // $warehouses->save();
      // }

      $warehouses->delete();
    } catch (\Exception $e) {
      return response()->json(
        ['message' => 'Error during bulk deletion ' . $e->getMessage()],
        500
      );
    }

    return $this->core->setResponse(
      'success',
      "Warehouses deleted",
      null,
      200
    );
  }

  private function validation($type = null, $request)
  {
    switch ($type) {
      case 'delete':
        $validator = [
          'uuids' => 'required|array',
          'uuids.*' => 'required|uuid',
          // 'uuids.*' => 'required|exists:warehouses,uuid',
        ];
        break;
      case 'create' || 'update':
        $validator = [
          // '*.country_uuid' => 'string|max:255|min:2',
          '*.code' => 'string|max:25|min:2',
          '*.name' => 'string|max:255|min:2',
          '*.phone' => 'string|max:25',
          '*.mobile_phone' => 'string|max:25',
          '*.email' => 'string|max:25',
          '*.province' => 'required|string|max:100|min:2',
          '*.city' => 'required|string|max:100|min:2',
          '*.district' => 'required|string|max:100|min:2',
          '*.village' => 'required|string|max:100|min:2',
          '*.zip_code' => 'string|max:100|min:2',
          '*.details' => 'string|max:100|min:2',
          '*.description' => 'string|max:100|min:2',
          '*.notes' => 'string|max:100|min:2',
          '*.remarks' => 'string|max:100|min:2',
          '*.status' => 'in:0,1,2,3',
          // '*.created_by' => 'required|string|min:4',
        ];
        break;
      default:
        $validator = [];
    }

    return Validator::make($request->all(), $validator);
  }
}
