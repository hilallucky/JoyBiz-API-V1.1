<?php

namespace App\Http\Controllers;

use App\Models\Calculations\Transactions\CalculationPointMember;
use App\Models\Members\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CalculatePointsController extends Controller
{
    public function calculatePointsRecursively(Request $request, $sponsorUuid)
    {
        $sponsorUuid = $sponsorUuid == 0 ? null : $sponsorUuid;

        // Calculate personal points for each member
        DB::table('calculation_point_members')
            ->where('sponsor_uuid', $sponsorUuid)
            ->update([
                'p_pv' => DB::raw('(SELECT COALESCE(SUM(total_pv), 0) FROM order_headers WHERE member_uuid = calculation_point_members.member_uuid)'),
            ]);

        // Calculate group points for each member's group
        DB::table('calculation_point_members')
            ->where('sponsor_uuid', $sponsorUuid)
            ->update([
                'g_pv' => DB::raw('(SELECT COALESCE(SUM(g_pv), 0) FROM calculation_point_members c WHERE sponsor_uuid = c.member_uuid)'),
            ]);

        // Recursively calculate points for child members
        $childMembers = DB::table('members')
            ->where('sponsor_uuid', $sponsorUuid)
            ->get();

        foreach ($childMembers as $childMember) {
            $this->calculatePointsRecursively($request, $childMember->uuid);
        }

        return 'success';
    }

    public function calculatePointsRecursively_v2($start, $end)
    {
        try {
            DB::beginTransaction();
            $processUuid = Str::uuid()->toString();
            // Retrieve all members with their transactions
            $members = Member::with('transactions')
                ->whereBetween(
                    DB::raw('created_at::date'),
                    [$start, $end]
                )
                ->get();

            // $members = Member::all();

            foreach ($members as $member) {
                $personalPV = $this->calculatePersonal($member, 'total_pv', $start, $end);
                $groupPV = $this->calculateGroup($member, 'total_pv', $start, $end);

                $personalBV = $this->calculatePersonal($member, 'total_bv', $start, $end);
                $groupBV = $this->calculateGroup($member, 'total_bv', $start, $end);

                $personalXV = $this->calculatePersonal($member, 'total_xv', $start, $end);
                $groupXV = $this->calculateGroup($member, 'total_xv', $start, $end);

                $personalRV = $this->calculatePersonal($member, 'total_rv', $start, $end);
                $groupRV = $this->calculateGroup($member, 'total_rv', $start, $end);

                $totalAmount = $this->calculatePersonal($member, 'total_amount', $start, $end);
                $totalAmountSummary = $this->calculatePersonal($member, 'total_amount_summary', $start, $end);

                // Save the calculated points in the calculation_point_members table
                CalculationPointMember::create([
                    'uuid' => Str::uuid()->toString(),
                    'process_uuid' => $processUuid,
                    'member_uuid' => $member->uuid,
                    'sponsor_uuid' => $member->sponsor_uuid,
                    'total_amount' => $totalAmount,
                    'total_amount_summary' => $totalAmountSummary,
                    'start_date' => $start,
                    'end_date' => $end,
                    'p_pv' => $personalPV,
                    'p_bv' => $personalBV,
                    'p_xv' => $personalXV,
                    'p_rv' => $personalRV,
                    'g_pv' => $groupPV,
                    'g_bv' => $groupBV,
                    'g_xv' => $groupXV,
                    'g_rv' => $groupRV,
                    'created_date' => Carbon::now()->format('Y-m-d H:i:s'),
                    // Add transaction_start_date and transaction_end_date based on your logic
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return $this->core->setResponse(
                'error',
                'Calculation fail to created. == ' . $e->getMessage(),
                [],
                FALSE,
                500
            );
        }

        return "Points calculated successfully!";
    }

    private function calculatePersonal(Member $member, $type, $start, $end)
    {
        // Calculate personal points based on the member's transactions
        return $member->transactions->sum($type);
    }

    private function calculateGroup(Member $member, $type, $start, $end)
    {
        // Calculate group points recursively by fetching the sponsor's group points
        $sponsor = $member->sponsor;

        if (!$sponsor) {
            // If there's no sponsor, use personal points as group points
            return $this->calculatePersonal($member, $type, $start, $end);
        }

        // Calculate group points by adding the sponsor's group points
        // with the personal points of the current member
        return $this->calculateGroup($sponsor, $type, $start, $end)
            + $this->calculatePersonal($member, $type, $start, $end);
    }
}
