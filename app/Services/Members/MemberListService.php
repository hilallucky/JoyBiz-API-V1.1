<?php

namespace App\Services\Members;

use app\Libraries\Core;
use App\Models\Members\Member;
use Illuminate\Support\Facades\DB;

class MemberListService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    public function getMemberList($request)
    {
        DB::enableQueryLog();

        // Get member list
        $members = Member::select(
            'id',
            'uuid',
            'first_name',
            'last_name',
            'phone',
            'sponsor_id',
            'sponsor_uuid',
            'placement_id',
            'placement_uuid',
            'status',
            'created_by',
            'updated_by',
            'deleted_by',
        )->with('sponsor', 'placement', 'user');

        if ($request->input('start') && $request->input('end')) {
            $start = $request->input('start');
            $end = $request->input('end');

            $members = $members->whereBetween('created_at', [$start, $end]);
        }

        $members = $members->orderBy('created_at', 'asc')->get();

        $query = DB::getQueryLog();
        // dd($query);

        if (!$members) {
            return $this->core->setResponse(
                'error',
                'Member not exist.',
                NULL,
                FALSE,
                400
            );
        }

        $memberDetails = collect();

        $no = 0;
        foreach ($members as $member) {
            $memberData = $member->toArray();

            if ($memberData['sponsor']) {
                unset($memberData['sponsor']);

                $memberData['sponsor']['id'] = $member->sponsor->id;
                $memberData['sponsor']['uuid'] = $member->sponsor->uuid;
                $memberData['sponsor']['first_name'] = $member->sponsor->first_name;
                $memberData['sponsor']['last_name'] = $member->sponsor->first_name;
            }

            if ($memberData['placement']) {
                unset($memberData['placement']);

                $memberData['placement']['id'] = $member->placement->id;
                $memberData['placement']['uuid'] = $member->placement->uuid;
                $memberData['placement']['first_name'] = $member->placement->first_name;
                $memberData['placement']['last_name'] = $member->placement->first_name;
            }

            if ($memberData['user']) {
                unset($memberData['user']);

                $memberData['user']['id'] = $member->user->id;
                $memberData['user']['uuid'] = $member->user->uuid;
                $memberData['user']['email'] = $member->user->email;
                $memberData['user']['password'] = $member->user->password;
            }

            $remove = [
                'user_uuid',
                'sponsor_id',
                'sponsor_uuid',
                'placement_id',
                'placement_uuid'
            ];

            $memberData = array_diff_key(
                $memberData,
                array_flip($remove)
            );

            $memberDetails->push($memberData);
        }

        return $this->core->setResponse(
            'success',
            'Member list.',
            $memberDetails,
        );
    }
}
