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
    $voucher = Voucher::with('member')->where('member_uuid', $member_uuid)->first();
    return ['saldo' => $voucher ? $voucher->saldo : 0];
  }

  public function use($member_uuid, $amount, $code_trans)
  {
    $userlogin = null;
    if (Auth::check()) {
      $user = Auth::user();
      $userlogin = $user->uuid;
    }

    $voucher = Voucher::where('member_uuid', $member_uuid)->first()->lockForUpdate();
    $now = Carbon::now();
    $sDate = Carbon::now()->startOfMonth();
    $eDate = $now->endOfMonth();
    $index = WalletDetail::whereBetween(
      'created_at',
      DB::raw('created_at::date'),
      [$sDate, $eDate]
    )
      ->count() + 1;

    $code = "UVCI" . $now->year . $now->format('m') . $index;
    $fullName = $user->first_name . ($user->last_name ? ' ' . $user->last_name : '');
    $note = "Voucher dipakai oleh " . $fullName .
      " pada " . $now . " untuk transaksi " . $code_trans . " issued_by " . $fullName;

    try {
      DB::beginTransaction();

      VoucherDetail::create([
        'member_uuid' => $member_uuid,
        'code' => $code, 'debit' => encrypt(0),
        'credit' => encrypt($amount),
        'note' => $note,
        'transaction_uuid' => $code_trans
      ]);

      $voucher->saldo = encrypt(decrypt($voucher->saldo) - $amount);
      $voucher->save();

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e);
    }

    return $code;
  }
}
