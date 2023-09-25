<?php

namespace App\Services\Calculations\Transactions;

use app\Libraries\Core;
use App\Models\Calculations\Transactions\CalculationPointMember;
use App\Models\Orders\Production\OrderHeader;
use App\Models\Orders\Temporary\OrderHeaderTemp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MemberSummaryService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    public function getTransactionTempSummaries($start, $end)
    {
        $data = OrderHeaderTemp::select(
            'member_uuid',
            DB::raw('SUM(total_amount) as total_amount'),
            DB::raw('SUM(total_pv) as total_pv'),
            DB::raw('SUM(total_xv) as total_xv'),
            DB::raw('SUM(total_bv) as total_bv'),
            DB::raw('SUM(total_rv) as total_rv'),
        )
            ->with('member:uuid,first_name,last_name,created_at')
            ->whereBetween(
                DB::raw('created_at::date'),
                [$start, $end]
            )
            ->groupBy('member_uuid')
            ->get();

        return $data;
    }

    public function getTransactionSummaries($start, $end)
    {
        $data = OrderHeader::select(
            'member_uuid',
            DB::raw('SUM(total_amount) as total_amount'),
            DB::raw('SUM(total_pv) as total_pv'),
            DB::raw('SUM(total_xv) as total_xv'),
            DB::raw('SUM(total_bv) as total_bv'),
            DB::raw('SUM(total_rv) as total_rv'),
        )
            ->with('member:uuid,first_name,last_name,created_at')
            ->whereBetween(
                DB::raw('created_at::date'),
                [$start, $end]
            )
            ->groupBy('member_uuid')
            ->get();

        return $data;
    }

    public function calculatePoint($start, $end)
    {
        $userUuid = null;
        $newCalculationAdd = null;
        $uuids = collect();

        // Check Auth & update user uuid to deleted_by
        if (Auth::check()) {
            $user = Auth::user();
            $userUuid = $user->uuid;
        }

        try {
            DB::beginTransaction();

            $getDatas = $this->getTransactionSummaries($start, $end);

            foreach ($getDatas as $data) {
                // New Calculation;
                $newCalculation = [
                    'uuid' => Str::uuid()->toString(),
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'member_uuid' => $data['rank_uuid'],
                    'rank_uuid' => $data['remarks'],
                    'total_discount_value' => $data['total_discount_value'],
                    'total_discount_value_amount' => $data['total_discount_value_amount'],
                    'total_price_after_discount' => $data['total_price_after_discount'],
                    'total_amount' => $data['total_amount'],
                    'total_shipping_charge' => $data['total_shipping_charge'],
                    'total_payment_charge' => $data['total_payment_charge'],
                    'total_amount_summary' => $data['total_amount_summary'],
                    'total_pv' => $data['total_pv'],
                    'total_xv' => $data['total_xv'],
                    'total_bv' => $data['total_bv'],
                    'total_rv' => $data['total_rv'],
                    'created_by' => $userUuid,
                ];

                // Insert into order_headers_temp
                $newCalculationAdd = new CalculationPointMember($newCalculation);
                $newCalculationAdd->save();

                $uuids->push($newCalculationAdd->uuid);
            }

            // Update in OrderHeader
            OrderHeader::lockForUpdate()
                // ->whereIn('uuid', $uuids)
                ->whereBetween(
                    DB::raw('created_at::date'),
                    [$start, $end]
                )
                ->update([
                    'calculation_point_members_uuid' => $newCalculationAdd->uuid,
                    'calculation_date' => Carbon::now()->format('Y-m-d H:i:s')
                ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Order fail to created. == ' . $e->getMessage(),
                [],
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Calculation created.',
            $newCalculationAdd,
            false,
            201
        );
    }
}
