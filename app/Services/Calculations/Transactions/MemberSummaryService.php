<?php

namespace App\Services\Calculations\Transactions;

use app\Libraries\Core;
use App\Models\Calculations\Transactions\CalculationPointMember;
use App\Models\Orders\Production\OrderHeader;
use App\Models\Orders\Temporary\OrderHeaderTemp;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

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
        DB::enableQueryLog();

        $data = OrderHeader::select(
            'member_uuid',
            DB::raw('SUM(total_discount_value) as total_discount_value'),
            DB::raw('SUM(total_discount_value_amount) as total_discount_value_amount'),
            DB::raw('SUM(total_price_after_discount) as total_price_after_discount'),
            DB::raw('SUM(total_amount) as total_amount'),
            DB::raw('SUM(total_shipping_charge) as total_shipping_charge'),
            DB::raw('SUM(total_payment_charge) as total_payment_charge'),
            DB::raw('SUM(total_amount_summary) as total_amount_summary'),
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

        // $query = DB::getQueryLog();
        // dd($query);

        return $data;
    }

    public function checkIfProcessIsExist($start, $end)
    {
        $check = CalculationPointMember::whereBetween('start_date', [$start, $end])
            ->orWhere(function ($query) use ($start, $end) {
                $query->whereBetween('end_date', [$start, $end]);
            })->get();

        // dd($check->isEmpty()) ;
        if (!$check->isEmpty()) {
            return $this->core->setResponse(
                'error',
                "Calculation between date = $start to $end already exist.",
                [],
                FALSE,
                500
            );
        }

        return $this->calculatePointFromTransactions($start, $end);
    }

    public function calculatePointFromTransactions($start, $end)
    {
        $userUuid = null;
        $newCalculationAdd = null;

        // Check Auth & update user uuid to deleted_by
        if (Auth::check()) {
            $user = Auth::user();
            $userUuid = $user->uuid;
        }

        DB::enableQueryLog();
        try {
            DB::beginTransaction();

            $processUuid = Str::uuid()->toString();
            $getDatas = $this->getTransactionSummaries($start, $end);

            foreach ($getDatas as $data) {
                // New Calculation;
                $newCalculation = [
                    'uuid' => Str::uuid()->toString(),
                    'process_uuid' => $processUuid,
                    'start_date' => $start,
                    'end_date' => $end,
                    'member_uuid' => $data['member_uuid'],
                    'rank_uuid' => $data['rank_uuid'],
                    'total_amount' => $data['total_amount'],
                    'total_amount_summary' => $data['total_amount_summary'],
                    'p_pv' => $data['total_pv'],
                    'p_xv' => $data['total_xv'],
                    'p_bv' => $data['total_bv'],
                    'p_rv' => $data['total_rv'],
                    'g_xv' => $data['total_xv'],
                    'g_bv' => $data['total_bv'],
                    'g_rv' => $data['total_rv'],
                    'g_pv' => $data['total_pv'],
                    'created_by' => $userUuid,
                ];

                // Insert into order_headers_temp
                $newCalculationAdd = new CalculationPointMember($newCalculation);
                $newCalculationAdd->save();
            }

            // Update in OrderHeader
            OrderHeader::lockForUpdate()
                // ->whereIn('uuid', $uuids)
                ->whereBetween(
                    DB::raw('created_at::date'),
                    [$start, $end]
                )
                ->update([
                    'calculation_point_process_uuid' => $processUuid,
                    'calculation_date' => Carbon::now()->format('Y-m-d H:i:s')
                ]);

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

        return $this->core->setResponse(
            'success',
            'Calculation created.',
            $newCalculationAdd,
            false,
            201
        );
    }


    // public static function calculatePoints($start, $end, $processUuid)
    // {
    //     $results = self::with('children')->get();

    //     $formattedResults = [];

    //     foreach ($results as $result) {
    //         $formattedResults[] = [
    //             'id' => $result->id,
    //             'parent_id' => $result->parent_id,
    //             'points' => $result->points,
    //             'akumulasi_points' => $result->calculateAccumulatedPoints_V2(),
    //         ];
    //     }

    //     return $formattedResults;
    // }
}
