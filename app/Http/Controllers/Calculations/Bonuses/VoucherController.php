<?php

namespace App\Http\Controllers\Calculations\Bonuses;

use App\Http\Controllers\Controller;
use App\Services\Calculations\Bonuses\VoucherService;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
  private VoucherService $voucherService;

  public function __construct(
    VoucherService $voucherService,
  ) {
    $this->voucherService = $voucherService;
  }

  public function getByMember(Request $request)
  {
    return $this->voucherService->getByMember($request);
  }

  public function generateVouchers(Request $request)
  {
    return $this->voucherService->generateVouchers($request);
  }
}
