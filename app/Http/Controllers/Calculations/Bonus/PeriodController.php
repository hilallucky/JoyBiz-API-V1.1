<?php

namespace App\Http\Controllers\Calculations\Bonus;

use App\Http\Controllers\Controller;
use App\Services\Calculations\Bonus\PeriodService;
use Illuminate\Http\Request;

class PeriodController extends Controller
{


    private PeriodService $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    public function generateWeekPeriods(Request $request)
    {
        return $this->periodService->generateWeekPeriods($request);
    }
}
