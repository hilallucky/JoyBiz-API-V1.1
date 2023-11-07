<?php

namespace App\Http\Controllers\WMS;

use App\Http\Controllers\Controller;
use App\Services\WMS\StockDailyService;
use App\Services\WMS\StockPeriodService;
use Illuminate\Http\Request;

class StockController extends Controller
{
  private StockDailyService $stockDailyService;
  private StockPeriodService $periodService;

  public function __construct(StockDailyService $stockDailyService, StockPeriodService $periodService)
  {
    $this->stockDailyService = $stockDailyService;
    $this->periodService = $periodService;
  }

  public function generatePeriod(Request $request)
  {
    return $this->periodService->generatePeriod($request);
  }

  // //Get all product informations
  // public function index(Request $request)
  // {
  //   return $this->stockDailyService->index($request);
  // }

  //Get Active Period information by date
  public function getActivePeriod($date = null, $type = null)
  {
    return $this->periodService->getActivePeriod($date, $type);
  }

  //Create new stock period
  public function store(Request $request)
  {
    return $this->stockDailyService->index($request);
  }
}
