<?php

namespace App\Http\Controllers\WMS;

use App\Http\Controllers\Controller;
use App\Services\WMS\StockDailyServicesService;
use Illuminate\Http\Request;

class StockController extends Controller
{
  private StockDailyServicesService $stockDailyService;

  public function __construct(StockDailyServicesService $stockDailyService)
  {
      $this->stockDailyService = $stockDailyService;
  }

  //Get all product informations
  public function index(Request $request)
  {
      return $this->stockDailyService->index($request);
  }

  //Create new product price information
  public function store(Request $request)
  {
      return $this->stockDailyService->store($request);
  }

  // //Get product price information by ids
  // public function show(Request $request, $uuid)
  // {
  //     return $this->stockDailyService->show($request, $uuid);
  // }

  // //UpdateBulk product price information
  // public function updateBulk(Request $request)
  // {
  //     return $this->stockDailyService->updateBulk($request);
  // }

  // //Delete product price information by ids
  // public function destroyBulk(Request $request)
  // {
  //     return $this->stockDailyService->destroyBulk($request);
  // }
}
