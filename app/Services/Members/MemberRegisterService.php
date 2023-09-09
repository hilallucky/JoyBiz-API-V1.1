<?php

namespace App\Services\Members;

use app\Libraries\Core;
use App\Models\Members\Member;
use App\Models\Members\MemberAddress;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Nonstandard\Uuid;

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

        $status = "1";

        if ($request->has('status')) {
            $status = $request->status;
        }

        try {
            DB::beginTransaction();
            DB::enableQueryLog();

            // Add User
            $newUser = $this->createUser($request, $status);

            // Add 1st Level Member
            $member = new Member;
            $member->uuid = Str::uuid()->toString();
            $member->first_name = $request->first_name;
            $member->last_name = $request->last_name;
            $member->sponsor_id = $request->sponsor_id;
            $member->sponsor_uuid = $request->sponsor_uuid;
            $member->placement_id = $request->placement_id;
            $member->placement_uuid = $request->placement_uuid;
            $member->user_uuid = $newUser->uuid;
            $member->phone = $request->last_name;
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
    protected function generateDownlines(Member $member, $numLegs, $type, $address)
    {
        if ($type == "straight") {
            $legs = $numLegs / 2;
            $no = 1;
            for ($i = 0; $i < 2; $i++) { // 2 (right & left)
                $currentSponsorId = $member->id;
                $currentSponsorUUID = $member->uuid;
                for ($j = 0; $j < $legs; $j++) {
                    // Add New downline
                    $downline = Member::create([
                        'uuid' => Str::uuid()->toString(),
                        'first_name' => 'Downline ' . ($no++),
                        'sponsor_id' => $currentSponsorId,
                        'sponsor_uuid' => $currentSponsorUUID,
                        'placement_id' => $member->placement_id,
                        'placement_uuid' => $member->placement_uuid,
                        'user_uuid' => $member->uuid,
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
                if ($i <= 1) {
                    $downline = Member::create([
                        'uuid' => Str::uuid()->toString(),
                        'first_name' => 'Downline ' . ($no++),
                        'sponsor_id' => $currentSponsorId,
                        'sponsor_uuid' => $currentSponsorUUID,
                        'placement_id' => $member->placement_id,
                        'placement_uuid' => $member->placement_uuid,
                        'user_uuid' => $member->uuid,
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
                            'uuid' => Str::uuid()->toString(),
                            'first_name' => 'Downline ' . ($no++),
                            'sponsor_id' => $leftSponsorId,
                            'sponsor_uuid' => $leftSponsorUUID,
                            'placement_id' => $member->placement_id,
                            'placement_uuid' => $member->placement_uuid,
                            'user_uuid' => $member->uuid,
                        ]);
                    } else {
                        $downline = Member::create([
                            'uuid' => Str::uuid()->toString(),
                            'first_name' => 'Downline ' . ($no++),
                            'sponsor_id' => $rightSponsorId,
                            'sponsor_uuid' => $rightSponsorUUID,
                            'placement_id' => $member->placement_id,
                            'placement_uuid' => $member->placement_uuid,
                            'user_uuid' => $member->uuid,
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
