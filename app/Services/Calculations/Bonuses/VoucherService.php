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

  public function getByMember($request)
  {
    // Get voucher cash/product
    $voucherHelper = new VoucherHelper();
    $voucher = $voucherHelper->getByMember($request->member_uuid);
    return $this->core->setResponse(
      'success',
      'Voucher generated.',
      $voucher,
      false,
      200
    );
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

  private function validation($type = null, $request)
  {
    switch ($type) {

      case 'getByMember':
        $validator = [
          'member_uuid' => 'required|uuid',
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
