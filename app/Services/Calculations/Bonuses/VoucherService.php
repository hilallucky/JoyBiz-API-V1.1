<?php

namespace App\Services\Calculations\Bonuses;

use App\Helpers\Bonuses\VoucherHelper;
use app\Libraries\Core;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VoucherService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }

  public function generateVouchers($request)
  {
    return $this->core->setResponse(
      'success',
      'Voucher generated.',
      $request,
      false,
      201
    );
  }

  public function getByMember($request)
  {
    // Get voucher cash/product
    $voucherHelper = new VoucherHelper();
    $voucher = $voucherHelper->getByMember($request->member_uuid);
    return $this->core->setResponse(
      'success',
      'Voucher for member = ' . $request->member_uuid . '.',
      $voucher,
      false,
      200
    );
  }

  public function usedByMember($request)
  {
    $validator = $this->validation(
      'usedByMember',
      $request
    );

    if ($validator->fails()) {
      return $this->core->setResponse(
        'error',
        $validator->messages()->first(),
        null,
        false,
        422
      );
    }

    // Get voucher cash/product
    $voucherHelper = new VoucherHelper();
    $voucher = $voucherHelper->use($request->member_uuid, $request->amount, $request->order_uuid);
    return $this->core->setResponse(
      'success',
      'Voucher used for member ' . $request->member_uuid . '.',
      $voucher,
      false,
      200
    );
  }

  private function validation($type = null, $request)
  {
    switch ($type) {
      case 'usedByMember':
        $validator = [
          'member_uuid' => 'required|uuid',
          'amount' => 'required|numeric',
          'order_uuid' => 'required|uuid',
        ];
        break;
      case 'create' || 'update':
        $validator = [];
        break;
      default:
        $validator = [];
    }

    return Validator::make(
      $request->all(),
      $validator
    );
  }
}
