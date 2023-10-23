<?php

namespace App\Services\Members;

use App\Http\Resources\Members\MemberShippingAddressResource;
use app\Libraries\Core;
use App\Models\Members\MemberShippingAddress;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MemberShippingAddressService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }


  //Get all Member Shipping Addresses informations
  public function index(Request $request)
  {

    $userlogin = null;
    if (Auth::check()) {
      $user = Auth::user();
      $userlogin = $user->uuid;
    }

    $query = MemberShippingAddress::query();

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

    if ($request->has('receiver_name')) {
      $query->where(
        'receiver_name',
        $request->input('receiver_name')
      );
    }

    if ($request->has('created_at')) {
      $dateRange = explode(',', $request->input('created_at'));
      if (count($dateRange) === 2) {
        $query->whereBetween(
          'created_at',
          $dateRange
        );
      }
    }

    if ($userlogin) {
      $query->where('receiver_name', $userlogin);
    }

    $shippingAddresses = $query->get();

    $shippingAddressList = MemberShippingAddressResource::collection($shippingAddresses);

    return $this->core->setResponse(
      'success',
      'Shipping Address Founded.',
      $shippingAddressList
    );
  }

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

    if ($request->has('status')) {
      $status = $request->status;
    }

    try {
      DB::beginTransaction();

      $userlogin = null;
      if (Auth::check()) {
        $user = Auth::user();
        $userlogin = $user->uuid;
      }

      // Add Member Shipping Address
      $shippingAddress = new MemberShippingAddress;
      $shippingAddress->uuid = Str::uuid();
      $shippingAddress->member_uuid = $request->member_uuid;
      $shippingAddress->receiver_name = $request->receiver_name;
      $shippingAddress->receiver_phone = $request->receiver_phone;
      $shippingAddress->city_uuid = $request->city_uuid;
      $shippingAddress->zip_code = $request->zip_code;
      $shippingAddress->province = $request->province;
      $shippingAddress->city = $request->city;
      $shippingAddress->district = $request->district;
      $shippingAddress->village = $request->village;
      $shippingAddress->details = $request->details;
      $shippingAddress->notes = $request->notes;
      $shippingAddress->remarks = $request->remarks;
      $shippingAddress->status = $request->status ? $request->status : 1;
      $shippingAddress->latitude = $request->latitude ? $request->latitude : null;
      $shippingAddress->longitude = $request->longitude ? $request->longitude : null;
      $shippingAddress->created_by = $userlogin;
      $shippingAddress->updated_by = $userlogin;
      $shippingAddress->status = $status;
      $shippingAddress->save();

      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        'Member Shipping Address fail to created. == ' . $e->getMessage(),
        NULL,
        FALSE,
        500
      );
    }
    return $this->core->setResponse(
      'success',
      'Member Shipping Address created successfully',
      $shippingAddress,
      null,
      201
    );
  }

  //Get Member Shipping Address information by ids
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
    $status = "0";

    if ($request->has('status')) {
      $status = $request->input('status', "1");
    }

    $shippingAddress = MemberShippingAddress::where([
      'uuid' => $uuid,
      'status' => $status
    ])->get();

    if (!isset($shippingAddress)) {
      return $this->core->setResponse(
        'error',
        'Member Shipping Address Not Found',
        NULL,
        FALSE,
        400
      );
    }

    $shippingAddressList = MemberShippingAddressResource::collection($shippingAddress);

    return $this->core->setResponse(
      'success',
      'Member Shipping Address Found',
      $shippingAddressList
    );
  }

  //UpdateBulk Member Shipping Address information
  public function update(Request $request)
  {
    $shippingAddressData = $request->all();

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

      // Check Auth & update user uuid 
      $userlogin = null;
      if (Auth::check()) {
        $user = Auth::user();
        $userlogin = $user->uuid;
      }

      $shippingAddress = MemberShippingAddress::lockForUpdate()
        ->where(
          'uuid',
          $shippingAddressData['uuid']
        )->firstOrFail();

      $shippingAddress->update([
        'receiver_name' => $shippingAddressData['receiver_name'],
        'receiver_phone' => $shippingAddressData['receiver_phone'],
        'city_uuid' => $shippingAddressData['city_uuid'],
        'zip_code' => $shippingAddressData['zip_code'],
        'province' => $shippingAddressData['province'],
        'city' => $shippingAddressData['city'],
        'district' => $shippingAddressData['district'],
        'village' => $shippingAddressData['village'],
        'details' => $shippingAddressData['details'],
        'notes' => $shippingAddressData['notes'],
        'remarks' => $shippingAddressData['remarks'],
        'latitude' => $shippingAddressData['latitude'],
        'longitude' => $shippingAddressData['longitude'],
        'status' => $status,
        'updated_by' => $userlogin,
      ]);

      $shippingAddressList = $shippingAddress->get();

      $shippingAddressList = MemberShippingAddressResource::collection($shippingAddressList);

      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        "Member Shipping Address fail to updated. " . $e->getMessage(),
        NULL,
        FALSE,
        500
      );
    }

    return $this->core->setResponse(
      'success',
      'Member Shipping Address updated',
      $shippingAddressList
    );
  }

  //Update Patch Member Shipping Address information
  public function updatePatch(Request $request)
  {
    DB::enableQueryLog();
    $shippingAddressData = $request->all();

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

      // Check Auth & update user uuid
      $userlogin = null;
      if (Auth::check()) {
        $user = Auth::user();
        $shippingAddressData['updated_by'] = $user->uuid;
      }

      $shippingAddress = MemberShippingAddress::lockForUpdate()
        ->where(
          'uuid',
          $shippingAddressData['uuid']
        )->first();;

      $shippingAddress->update($shippingAddressData);

      $shippingAddressList = MemberShippingAddressResource::collection(
        $shippingAddress->where(
          'uuid',
          $shippingAddressData['uuid']
        )->get()
      );

      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        "Member Shipping Address fail to updated. " . $e->getMessage(),
        NULL,
        FALSE,
        500
      );
    }

    return $this->core->setResponse(
      'success',
      'Member Shipping Address updated',
      $shippingAddressList
    );
  }

  //Delete Member Shipping Address information by ids
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
    $shippingAddresss = null;
    try {
      $shippingAddresss = MemberShippingAddress::lockForUpdate()
        ->whereIn(
          'uuid',
          $uuids
        );

      // Compare the count of found UUIDs with the count from the request array
      if (
        !$shippingAddresss ||
        (count($shippingAddresss->get()) !== count($uuids))
      ) {
        return response()->json(
          ['message' => 'Member Shipping Addresss fail to deleted, because invalid uuid(s)'],
          400
        );
      }

      //Check Auth & update user uuid to deleted_by
      if (Auth::check()) {
        $user = Auth::user();
        $shippingAddresss->deleted_by = $user->uuid;
        $shippingAddresss->save();
      }

      $shippingAddresss->delete();
    } catch (\Exception $e) {
      return response()->json(
        ['message' => 'Error during bulk deletion ' . $e->getMessage()],
        500
      );
    }

    return $this->core->setResponse(
      'success',
      "Member Shipping Addresss deleted",
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
          'uuids.*' => 'required|in:sponsor,upline',
        ];

        break;

      case 'update':

        $validator = [
          'uuid' => 'required|uuid',
          'member_uuid' => 'required|uuid',
        ];

        break;

      case 'create':

        $validator = [
          'member_uuid' => 'required|uuid',
          'city_uuid' => 'required|uuid',
          'receiver_name' => 'required|string',
          'receiver_phone' => 'required|string',
          'zip_code' => 'required|numeric',
          'province' => 'required|string',
          'city' => 'required|string',
          'district' => 'required|string',
          'village' => 'string',
          'details' => 'required|string',
          'notes' => 'string',
          'remarks' => 'string',
          'status' => 'in:0,1',
          'latitude' => 'string',
          'longitude' => 'string',
        ];

        break;

      default:

        $validator = [];
    }

    return Validator::make($request->all(), $validator);
  }
}
