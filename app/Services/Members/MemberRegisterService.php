<?php

namespace App\Services\Members;

use app\Libraries\Core;
use App\Models\Members\Member;
use App\Models\Members\MemberAddress;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Nonstandard\Uuid;
use stdClass;

class MemberRegisterService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }

  public function register(Request $request)
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

    $status = "0";

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

      // Add User
      $newUser = $this->createUser($request, $status);

      // Add 1st Level Member
      $member = new Member;
      $member->uuid = Str::uuid();
      $member->first_name = $request->first_name;
      $member->last_name = $request->last_name;
      $member->id_no = $request->has($request->id_no) ? $request->id_no : null;
      $member->phone = $request->has($request->phone) ? $request->phone : null;
      $member->sponsor_id = $request->sponsor_id;
      $member->sponsor_uuid = $request->sponsor_uuid;
      $member->placement_id = $request->placement_id;
      $member->placement_uuid = $request->placement_uuid;
      $member->user_uuid = $newUser->uuid;
      $member->user_id = $newUser->user_id;
      $member->min_bv = $request->has($request->min_bv) ? $request->min_bv : 0;
      $member->created_by = $userlogin;
      $member->updated_by = $userlogin;
      $member->status = $status;
      $member->save();

      $this->address($member->uuid, (object)$request->address);

      $this->generateDownlines(
        $member,
        $request->auto_leg_to_generate,
        $request->type,
        $request->address
      );

      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        'Member fail to created. == ' . $e->getMessage(),
        NULL,
        FALSE,
        500
      );
    }
    return $this->core->setResponse(
      'success',
      'Member registered successfully',
      $member,
      null,
      201
    );
  }
  protected function generateDownlines(
    Member $member,
    $numLegs,
    $type,
    $address
  ) {

    if ($type == "straight") {
      $legs = $numLegs / 2;
      $no = 1;

      for ($i = 0; $i < 2; $i++) { // 2 (right & left)
        $currentSponsorId = $member->id;
        $currentSponsorUUID = $member->uuid;

        $newUser = new stdClass;

        $newUser->uuid = Str::uuid();
        $newUser->first_name = $member->first_name;
        $newUser->last_name = $member->last_name;
        $newUser->email = $i + 1 . $member->user->email;
        $newUser->password = null;

        $addNewUser = $this->createUser(
          $newUser,
          $member->status
        );

        for ($j = 0; $j < $legs; $j++) {
          // Add New downline
          $downline = Member::create([
            'uuid' => Str::uuid(),
            'first_name' => $member->first_name, //'Downline ' . ($no++),
            'last_name' => $member->last_name,
            'id_no' => $member->id_no,
            'phone' => $member->phone,
            'sponsor_id' => $member->placement_id, //$currentSponsorId,
            'sponsor_uuid' => $member->placement_uuid, //$currentSponsorUUID,
            'placement_id' => $currentSponsorId, //$member->placement_id,
            'placement_uuid' => $currentSponsorUUID, //$member->placement_uuid,
            'user_id' => $addNewUser->id,
            'user_uuid' => $addNewUser->uuid,
            'min_bv' => $member->min_bv,
            'created_by' => $addNewUser->uuid,
            'updated_by' => $addNewUser->uuid,
            'status' => $member->status,
          ]);

          $currentSponsorId = $downline->id;
          $currentSponsorUUID = $downline->uuid;
        }

        $this->address($downline->uuid, (object)$address);
      }
    } else if ($type == "branch") {
      $legs = $numLegs / 2;
      $no = 1;

      $currentSponsorId = $member->id;
      $currentSponsorUUID = $member->uuid;

      for ($i = 0; $i < $numLegs; $i++) { // 2 (right & left)

        $newUser = new stdClass;
        $newMemberUuid = Str::uuid();

        $newUser->uuid = $newMemberUuid;
        $newUser->first_name = $member->first_name;
        $newUser->last_name = $member->last_name;
        $newUser->email = $i + 1 . $member->user->email;
        $newUser->password = null;

        $addNewUser = $this->createUser(
          $newUser,
          $member->status
        );

        if ($i <= 1) {
          $downline = Member::create([
            'uuid' => $newMemberUuid,
            'first_name' => $member->first_name, //'Downline ' . ($no++),
            'last_name' => $member->last_name,
            'id_no' => $member->id_no,
            'phone' => $member->phone,
            'sponsor_id' => $member->placement_id, //$currentSponsorId,
            'sponsor_uuid' => $member->placement_uuid, //$currentSponsorUUID,
            'placement_id' => $currentSponsorId, //$member->placement_id,
            'placement_uuid' => $currentSponsorUUID, //$member->placement_uuid,
            'user_id' => $addNewUser->id,
            'user_uuid' => $addNewUser->uuid,
            'min_bv' => $member->min_bv,
            'created_by' => $addNewUser->uuid,
            'updated_by' => $addNewUser->uuid,
            'status' => $member->status,
          ]);

          if ($i == 0) {
            $leftSponsorId = $downline->id; //left
            $leftSponsorUUID = $downline->uuid; //left
          }

          if ($i == 1) {
            $rightSponsorId = $downline->id; //right
            $rightSponsorUUID = $downline->uuid; //left
          }
        }

        // Add Unit/Downline
        if ($i > 1) {
          if ($i % 2 == 0) { // set sponsor to the left
            $downline = Member::create([
              'uuid' => $newMemberUuid,
              'first_name' => $member->first_name, //'Downline ' . ($no++),
              'last_name' => $member->last_name,
              'id_no' => $member->id_no,
              'phone' => $member->phone,
              'sponsor_id' => $member->placement_id, //$leftSponsorId,
              'sponsor_uuid' => $member->placement_uuid, //$leftSponsorUUID,
              'placement_id' => $leftSponsorId, //$member->placement_id,
              'placement_uuid' => $leftSponsorUUID, //$member->placement_uuid,
              'user_id' => $addNewUser->id,
              'user_uuid' => $addNewUser->uuid,
              'min_bv' => $member->min_bv,
              'created_by' => $addNewUser->uuid,
              'updated_by' => $addNewUser->uuid,
              'status' => $member->status,
            ]);
          } else {
            $downline = Member::create([
              'uuid' => $newMemberUuid,
              'first_name' => $member->first_name, //'Downline ' . ($no++),
              'last_name' => $member->last_name,
              'id_no' => $member->id_no,
              'phone' => $member->phone,
              'sponsor_id' => $member->placement_id, //$rightSponsorId,
              'sponsor_uuid' => $member->placement_uuid, //$rightSponsorUUID,
              'placement_id' => $rightSponsorId, //$member->placement_id,
              'placement_uuid' => $rightSponsorUUID, //$member->placement_uuid,
              'user_id' => $addNewUser->id,
              'user_uuid' => $addNewUser->uuid,
              'min_bv' => $member->min_bv,
              'created_by' => $addNewUser->uuid,
              'updated_by' => $addNewUser->uuid,
              'status' => $member->status,
            ]);
          }
        }

        $this->address($downline->uuid, (object)$address);
      }
    }
  }

  public function createUser($request, $status)
  {
    $password = date_format(Carbon::now(), "Ymd");

    $uuid = Uuid::uuid4()->toString();

    $user = new User;
    $user->uuid = $uuid;
    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;
    $user->email = $request->email;
    $user->password = $request->password ? $request->password : Hash::make($password);
    $user->validation_code = Hash::make($uuid);
    $user->status = $status;
    $user->save();

    return $user;
  }

  public function address($memberUUID, $data)
  {
    $address = new MemberAddress();
    $address->uuid = Uuid::uuid4()->toString();;
    $address->member_uuid = $memberUUID;
    $address->zip_code = $data->zip_code;
    $address->province = $data->province;
    $address->city = $data->city;
    $address->district = $data->district;
    $address->village = $data->village;
    $address->details = $data->details;
    $address->notes = $data->notes;
    $address->remarks = $data->remarks;

    if (isset($address->city_uuid) && $address->city_uuid !== null) {
      $address->city_uuid = $data->city_uuid;
    }

    $address->save();

    return $address;
  }

  private function validation($type = null, $request)
  {
    switch ($type) {
      case 'delete':
        $validator = [
          'uuids' => 'required|array',
          'uuids.*' => 'required|in:sponsor,upline',
          // 'uuids.*' => 'required|exists:cities,uuid',
        ];
        break;
      case 'genealogy':
        $validator = [
          'uuid' => 'required',
          'type' => 'required|in:sponsor,upline',
        ];
        break;
      case 'create' || 'update':
        $validator = [
          'first_name' => 'required',
          'last_name' => 'required',
          //  'email' => 'required|email|unique:members',
          // 'country_id' => 'required',
          'sponsor_id' => 'required|numeric',
          'sponsor_uuid' => 'required|uuid',
          'placement_id' => 'required|numeric',
          'placement_uuid' => 'required|uuid',
          'phone' => 'required|string',
          'auto_leg_to_generate' => 'required|numeric|min:2|max:6',
          'status' => 'in:0,1,2,3',
          'min_bv' => 'numeric',
          'activated_at' => 'date',
          'address' => 'required',
          // 'address.city_uuid' => 'string',
          'address.zip_code' => 'required|string',
          'address.province' => 'required|string',
          'address.city' => 'required|string',
          'address.district' => 'required|string',
          'address.village' => 'required|string',
          'address.details' => 'string',
          'address.notes' => 'string',
          'address.remarks' => 'string',
          // '*.created_by' => 'required|string|min:4',
        ];
        break;
      default:
        $validator = [];
    }

    return Validator::make($request->all(), $validator);
  }
}
