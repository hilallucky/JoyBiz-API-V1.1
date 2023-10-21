<?php

namespace App\Services\Configs;

use App\Http\Resources\Configs\PaymentTypeResource;
use app\Libraries\Core;
use App\Models\Configs\PaymentType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentTypeService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }

  //Get all Payment Type
  public function index(Request $request)
  {
    $query = PaymentType::query();

    // Apply filters based on request parameters
    if ($request->has('status')) {
      $query->where(
        'status',
        $request->input('status')
      );
    } else {
      $query->where(
        'status',
        1
      );
    }

    if ($request->has('name')) {
      $param = $request->input('name');

      $query = $query->where(
        function ($q) use ($param) {
          $q->orWhere(
            'code',
            'ilike',
            '%' . $param . '%'
          )->orWhere(
            'name',
            'ilike',
            '%' . $param . '%'
          )->orWhere(
            'description',
            'ilike',
            '%' . $param . '%'
          );
        }
      );
    }

    $paymentType = $query->get();

    $paymentTypeList = PaymentTypeResource::collection($paymentType);

    return $this->core->setResponse(
      'success',
      'Payment Type Founded',
      $paymentTypeList
    );
  }

  //Create new Payment Type
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

      $paymentTypes = $request->all();
      foreach ($paymentTypes as $paymentType) {
        if (isset($paymentType['status'])) {
          $status = $paymentType['status'];
        }

        $newPaymentType = [
          'uuid' => Str::uuid()->toString(),
          'ref_uuid' => $paymentType['ref_uuid'] ? $paymentType['ref_uuid'] : null,
          'code' => $paymentType['code'],
          'name' => $paymentType['name'],
          'description' => $paymentType['description'],
          'charge_percent' => $paymentType['charge_percent'],
          'charge_amount' => $paymentType['charge_amount'],
          'effect' => $paymentType['effect'],
          'status_web' => $paymentType['status_web'],
          'remarks' => $paymentType['remarks'],
          'status' => $status,
          // 'created_by' => $user->uuid,
        ];

        $newPaymentTypeAdd = new PaymentType($newPaymentType);
        $newPaymentTypeAdd->save();

        $newPaymentTypes[] = $newPaymentTypeAdd->uuid;
      }

      $paymentTypeList = PaymentType::whereIn(
        'uuid',
        $newPaymentTypes
      )->get();

      $paymentTypeList = PaymentTypeResource::collection($paymentTypeList);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        'PaymentType fail to created. ' . $e->getMessage(),
        NULL,
        FALSE,
        500
      );
    }

    return $this->core->setResponse(
      'success',
      'PaymentType created',
      $paymentTypeList,
      false,
      201
    );
  }

  //Get Payment Type by ids
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

    $paymentType = PaymentType::where([
      'uuid' => $uuid,
      'status' => $status
    ])->get();

    if (!isset($paymentType)) {
      return $this->core->setResponse(
        'error',
        'PaymentType Not Found',
        NULL,
        FALSE,
        400
      );
    }

    $paymentTypeList = PaymentTypeResource::collection($paymentType);

    return $this->core->setResponse(
      'success',
      'PaymentType Found',
      $paymentTypeList
    );
  }

  //UpdateBulk Payment Type
  public function updateBulk(Request $request)
  {
    $paymentType = $request->all();

    $validator = $this->validation(
      'update',
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

    $status = "1";

    try {
      DB::beginTransaction();

      // Check Auth & update user uuid to deleted_by
      if (Auth::check()) {
        $user = Auth::user();
      }

      foreach ($paymentType as $paymentTypeData) {
        if (isset($paymentTypeData['status'])) {
          $status = $paymentTypeData['status'];
        }

        $paymentType = PaymentType::lockForUpdate()
          ->where(
            'uuid',
            $paymentTypeData['uuid']
          )->firstOrFail();

        $paymentTypeUpdate = [
          'code' => $paymentTypeData['code'],
          'name' => $paymentTypeData['name'],
          'description' => $paymentTypeData['description'],
          'charge_percent' => $paymentTypeData['charge_percent'],
          'charge_amount' => $paymentTypeData['charge_amount'],
          'effect' => $paymentTypeData['effect'],
          'status_web' => $paymentTypeData['status_web'],
          'remarks' => $paymentTypeData['remarks'],
          'status' => $status,
          // 'updated_by' => $user->uuid,
        ];

        if (isset($paymentTypeData['ref_uuid']) && $paymentTypeData['ref_uuid'] !== "") {
          $paymentTypeUpdate['ref_uuid'] = $paymentTypeData['ref_uuid'];
        }

        $paymentType->update($paymentTypeUpdate);

        $updatedpaymentTypes[] = $paymentType->toArray();
      }

      $paymentTypeList = PaymentType::whereIn(
        'uuid',
        array_column($updatedpaymentTypes, 'uuid')
      )->get();

      $paymentTypeList = PaymentTypeResource::collection($paymentTypeList);

      DB::commit();
    } catch (QueryException $e) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        'PaymentType fail to updated. ' . $e->getMessage(),
        NULL,
        FALSE,
        500
      );
    } catch (\Exception $ex) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        "PaymentType fail to updated. " . $ex->getMessage(),
        NULL,
        FALSE,
        500
      );
    }

    return $this->core->setResponse(
      'success',
      'PaymentType updated',
      $paymentTypeList
    );
  }

  //Delete PaymentType by ids
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
    $paymentType = null;
    try {
      $paymentType = PaymentType::lockForUpdate()
        ->whereIn(
          'uuid',
          $uuids
        );

      // Compare the count of found UUIDs with the count from the request array
      if (
        !$paymentType ||
        (count($paymentType->get()) !== count($uuids))
      ) {
        return response()->json(
          ['message' => 'Countries fail to deleted, because invalid uuid(s)'],
          400
        );
      }

      //Check Auth & update user uuid to deleted_by
      // if (Auth::check()) {
      //     $user = Auth::user();
      // $paymentType->deleted_by = $user->uuid;
      // $paymentType->save();
      // }

      $paymentType->delete();
    } catch (\Exception $e) {
      return response()->json(
        ['message' => 'Error during bulk deletion ' . $e->getMessage()],
        500
      );
    }

    return $this->core->setResponse(
      'success',
      "Payment Types deleted",
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
          // 'uuids.*' => 'required|exists:paymentType,uuid',
        ];

        break;

      case 'create' || 'update':

        $validator = [
          '*.ref_uuid' => 'uuid|nullable',
          '*.code' => 'required|string|max:255|min:2',
          '*.name' => 'required|string|max:255|min:2',
          '*.description' => 'required|string|max:255|min:2',
          // '*.charge_percent' => 'numeric',
          // '*.charge_amount' => 'numeric',
          '*.effect' => 'in:-,+',
          '*.status_web' => 'in:0,1,2,3',
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
