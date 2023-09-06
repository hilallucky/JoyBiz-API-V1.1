<?php

namespace App\Services\Members;

use app\Libraries\Core;
use App\Models\Members\Member;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Nonstandard\Uuid;

class MemberService
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

            // Add 1st Level Member
            $member = new Member;
            $member->uuid = Str::uuid()->toString();
            $member->first_name = $request->first_name;
            $member->last_name = $request->last_name;
            $member->sponsor_id = $request->sponsor_id;
            $member->sponsor_uuid = $request->sponsor_uuid;
            $member->upline_id = $request->upline_id;
            $member->upline_uuid = $request->upline_uuid;
            $member->phone = $request->last_name;
            $member->status = $status;
            $member->save();

            // Add User
            $this->createUser($request, $status);

            $this->generateDownlines(
                $member, $request->auto_leg_to_generate,
                $request->type
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Member fail to created.' . $e->getMessage(),
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
    protected function generateDownlines(Member $member, $numLegs, $type)
    {
        if ($type == "straight") {
            $legs = $numLegs / 2;
            $no = 1;
            for ($i = 0; $i < 2; $i++) { // 2 (right & left)
                $currentSponsorId = $member->id;
                $currentSponsorUUID = $member->sponsor_uuid;
                for ($j = 0; $j < $legs; $j++) {
                    // Add New downline
                    $downline = Member::create([
                        'uuid' => Str::uuid()->toString(),
                        'first_name' => 'Downline ' . ($no++),
                        'sponsor_id' => $currentSponsorId,
                        'sponsor_uuid' => $currentSponsorUUID,
                        'upline_id' => $member->upline_id,
                        'upline_uuid' => $member->upline_uuid,
                    ]);

                    $currentSponsorId = $downline->id;
                    $currentSponsorUUID = $downline->sponsor_uuid;
                }
            }
        } else if ($type == "branch") {
            $legs = $numLegs / 2;
            $no = 1;

            $currentSponsorId = $member->id;
            $currentSponsorUUID = $member->sponsor_uuid;

            for ($i = 0; $i < $numLegs; $i++) { // 2 (right & left)
                if ($i <= 1) {
                    $downline = Member::create([
                        'uuid' => Str::uuid()->toString(),
                        'first_name' => 'Downline ' . ($no++),
                        'sponsor_id' => $currentSponsorId,
                        'sponsor_uuid' => $currentSponsorUUID,
                        'upline_id' => $member->upline_id,
                        'upline_uuid' => $member->upline_uuid,
                    ]);

                    if ($i == 0) {
                        $leftSponsorId = $downline->id; //left
                        $leftSponsorUUID = $downline->sponsor_uuid; //left
                    }

                    if ($i == 1) {
                        $rightSponsorId = $downline->id; //right
                        $rightSponsorUUID = $downline->sponsor_uuid; //left
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
                            'upline_id' => $member->upline_id,
                            'upline_uuid' => $member->upline_uuid,
                        ]);
                    } else {
                        $downline = Member::create([
                            'uuid' => Str::uuid()->toString(),
                            'first_name' => 'Downline ' . ($no++),
                            'sponsor_id' => $rightSponsorId,
                            'sponsor_uuid' => $rightSponsorUUID,
                            'upline_id' => $member->upline_id,
                            'upline_uuid' => $member->upline_uuid,
                        ]);
                    }
                }
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
                    'sponsor_id' => 'required',
                    'phone' => 'required|string',
                    'auto_leg_to_generate' => 'required|numeric|min:2|max:6',
                    'status' => 'in:0,1,2,3',
                    // '*.created_by' => 'required|string|min:4',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }

    public function getGenealogy($uuid, $type = 'sponsor')
    {
        // Get member based on uuid
        $member = Member::where('uuid', $uuid)->first();

        $genealogy = collect();
        if (!$member) {
            return $this->core->setResponse(
                'error',
                'Member not exist.',
                NULL,
                FALSE,
                400
            );
        }
        $genealogy = $genealogy->concat(
            $this->getGenealogyRecursive($member, $type)
        );

        return $genealogy;
    }

    private function getGenealogyRecursive($member, $type)
    {
        $result = collect([$member]);

        if ($type == 'sponsor') {
            $up_id = 'sponsor_id';
        } else if ($type == 'upline') {
            $up_id = 'upline_id';
        }

        $children = Member::where($up_id, $member->id)->get();

        foreach ($children as $child) {
            $result = $result->concat(
                $this->getGenealogyRecursive($child, $type)
            );
        }

        return $result;
    }
}
