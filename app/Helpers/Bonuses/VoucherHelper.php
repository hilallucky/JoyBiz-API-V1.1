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
use Illuminate\Support\Str;

class VoucherHelper extends Model
{
  public function getByMember($member_uuid)
  {
    $voucher = Voucher::with('member')->where('member_uuid', $member_uuid)->first();
    return ['saldo' => $voucher ? $voucher->saldo : 0];
  }

  public function use($member_uuid, $amount, $order_uuid)
  {
    DB::enableQueryLog();

    $userlogin = null;
    if (Auth::check()) {
      $user = Auth::user();
      $userlogin = $user->uuid;
    }

    $voucher = Voucher::with('member')->where('member_uuid', $member_uuid)->lockForUpdate()->first();
    $now = Carbon::now();
    $sDate = Carbon::now()->startOfMonth();
    $eDate = $now->endOfMonth();

    $index = WalletDetail::whereBetween(
      DB::raw('created_at::date'),
      [$sDate, $eDate]
    )
      ->count() + 1;

    $code = "UVCI" . $now->year . $now->format('m') . $index;
    $fullName = $voucher->member->first_name . ($voucher->member->last_name ? ' ' . $voucher->member->last_name : '');
    $note = "Voucher dipakai oleh " . $fullName .
      " pada " . $now . " untuk transaksi " . $order_uuid . " issued_by " . $fullName;

    try {
      DB::beginTransaction();

      VoucherDetail::create([
        'uuid' => Str::uuid(),
        'member_uuid' => $member_uuid,
        'code' => $code,
        'debit' => encrypt(0),
        'credit' => encrypt($amount),
        'transaction_uuid' => $order_uuid,
        'note' => $note,
      ]);

      $voucher->saldo = encrypt($voucher->saldo - $amount);
      $voucher->save();

      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e);
      dd($e);
    }

    return $code;
  }
}
