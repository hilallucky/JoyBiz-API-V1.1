<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Members\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            //  'email' => 'required|email|unique:members',
            // 'country_id' => 'required',
            'sponsor_id' => 'required',
            'phone' => 'required|string',
            'status' => 'required|numeric',
            'auto_leg_to_generate' => 'required|numeric|min:2|max:6',
        ]);


        try {
            DB::beginTransaction();
            DB::enableQueryLog();

            $member = new Member;
            $member->uuid = Str::uuid()->toString();
            $member->first_name = $request->first_name;
            $member->last_name = $request->last_name;
            $member->sponsor_id = $request->sponsor_id;
            $member->phone = $request->last_name;
            $member->status = $request->status;
            $member->save();

            $this->generateDownlines(
                $member, $request->auto_leg_to_generate,
                $request->type
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Product fail to created.' . $e->getMessage(),
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
                for ($j = 0; $j < $legs; $j++) {
                    // Add New downline
                    $downline = Member::create([
                        'uuid' => Str::uuid()->toString(),
                        'first_name' => 'Downline ' . ($no++),
                        'sponsor_id' => $currentSponsorId,
                    ]);

                    $currentSponsorId = $downline->id;
                }
            }
        } else if ($type == "branch") {
            $legs = $numLegs / 2;
            $no = 1;

            $currentSponsorId = $member->id;

            for ($i = 0; $i < $numLegs; $i++) { // 2 (right & left)
                if ($i <= 1) {
                    $downline = Member::create([
                        'uuid' => Str::uuid()->toString(),
                        'first_name' => 'Downline ' . ($no++),
                        'sponsor_id' => $currentSponsorId,
                    ]);

                    if ($i == 0) {
                        $leftSponsorId = $downline->id; //right
                    }

                    if ($i == 1) {
                        $rightSponsorId = $downline->id; //left
                    }
                }

                if ($i > 1) {
                    if ($i % 2 == 0) { // set sponsor to the left
                        $downline = Member::create([
                            'uuid' => Str::uuid()->toString(),
                            'first_name' => 'Downline ' . ($no++),
                            'sponsor_id' => $leftSponsorId,
                        ]);
                    } else {
                        $downline = Member::create([
                            'uuid' => Str::uuid()->toString(),
                            'first_name' => 'Downline ' . ($no++),
                            'sponsor_id' => $rightSponsorId,
                        ]);
                    }


                }
            }
        }

    }
}
