<?php

namespace App\Http\Controllers\WMS;

use App\Http\Controllers\Controller;
use App\Repositories\WMS\DORepository;
use App\Services\WMS\DOService;
use Illuminate\Http\Request;

class DOController extends Controller
{
  private DOService $doService;

  public function __construct(DOService $doService)
  {
    $this->doService = $doService;
  }

  public function index(Request $request)
  {
    return $this->doService->index($request);
  }

  public function store(Request $request)
  {
    return $this->doService->store($request);
  }
  
}
