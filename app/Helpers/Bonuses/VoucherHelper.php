<?php

namespace App\Helpers\Bonuses;

use App\Models\Bonuses\CouponsAndRewards\Voucher;
use App\Models\Bonuses\CouponsAndRewards\VoucherDetail;
use App\Models\Bonuses\Wallets\WalletDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherHelper extends Model
{
  public function getByMember($member_uuid)
  {
    $voucher = Voucher::where('member_uuid', $member_uuid)->first();
    $saldo = decrypt($voucher->saldo);
    return $saldo;
  }

  public function useVoucher($member_uuid, $amount, $code_trans)
  {
    $userlogin = null;
    if (Auth::check()) {
      $user = Auth::user();
      $userlogin = $user->uuid;
    }

    $voucher = Voucher::where('member_uuid', $member_uuid)->first();
    $saldo = decrypt($voucher->saldo);

    $now = Carbon::now();
    $sDate = Carbon::now()->startOfMonth();
    $eDate = $now->endOfMonth();
    $index = WalletDetail::whereBetween(
      'created_at',
      DB::raw('created_at::date'),
      [$sDate, $eDate]
    ) //('created_at', [$sDate, $eDate])
      ->count() + 1;

    $code = "UVCI" . $now->year . $now->format('m') . $index;
    $note = "Voucher dipakai oleh " . $user->first_name . " pada " . $now . " untuk transaksi " . $code_trans . " issued_by " . $userlogin->first_name;

    try {
      DB::beginTransaction();
      $result = VoucherDetail::create(['member_uuid' => $member_uuid, 'code' => $code, 'debit' => encrypt(0), 'credit' => encrypt($amount), 'note' => $note]);

      $new_saldo = $saldo - $amount;
      $voucher->saldo = encrypt($new_saldo);
      $voucher->save();
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e);
    }

    return $code;
  }

  public function useByMember()
  {
  }
}
