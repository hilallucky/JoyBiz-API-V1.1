<?php

namespace App\Http\Controllers\WMS;

use App\Http\Controllers\Controller;
use App\Services\WMS\GetTransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
  private GetTransactionService $getTransactionService;

  public function __construct(GetTransactionService $getTransactionService)
  {
      $this->getTransactionService = $getTransactionService;
  }

  //Get all product informations
  public function index(Request $request)
  {
      return $this->getTransactionService->index($request);
  }

  //Create new product price information
  public function store(Request $request)
  {
      return $this->getTransactionService->store($request);
  }

  // //Get product price information by ids
  // public function show(Request $request, $uuid)
  // {
  //     return $this->getTransactionService->show($request, $uuid);
  // }

  // //UpdateBulk product price information
  // public function updateBulk(Request $request)
  // {
  //     return $this->getTransactionService->updateBulk($request);
  // }

  // //Delete product price information by ids
  // public function destroyBulk(Request $request)
  // {
  //     return $this->getTransactionService->destroyBulk($request);
  // }
}
