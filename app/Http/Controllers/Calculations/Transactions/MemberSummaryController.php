<?php

namespace App\Http\Controllers\Calculations\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Calculations\Transactions\CalculationPointMember;
use App\Models\Members\Member;
use App\Services\Calculations\Transactions\MemberSummaryService;
use Illuminate\Http\Request;

class MemberSummaryController extends Controller
{
    private MemberSummaryService $memberSummaryService;

    public function __construct(MemberSummaryService $memberSummaryService)
    {
        $this->memberSummaryService = $memberSummaryService;
    }
    public function getTransactionTempSummaries(Request $request, $start, $end)
    {
        return $this->memberSummaryService->getTransactionTempSummaries($start, $end);
    }
    public function getTransactionSummaries(Request $request, $start, $end)
    {
        return $this->memberSummaryService->getTransactionSummaries($start, $end);
    }

    public function calculatePointFromTransactions(Request $request, $start, $end)
    {
        return $this->memberSummaryService->checkIfProcessIsExist($start, $end);
    }

    public function getAccumulatedPoints(Request $request, $start, $end)
    {
        $results = Member::getAccumulatedPoints($start, $end);

        return response()->json($results);
    }
}
