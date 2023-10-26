<?php

namespace App\Helpers\Bonuses;

use App\Models\Bonuses\CouponsAndRewards\Voucher;
use App\Models\Bonuses\CouponsAndRewards\VoucherCashback;
use App\Models\Bonuses\CouponsAndRewards\VoucherCashbackDetail;
use App\Models\Bonuses\CouponsAndRewards\VoucherDetail;
use App\Models\Bonuses\Wallets\Wallet;
use App\Models\Bonuses\Wallets\WalletDetail;
use App\Models\Members\Member;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Money extends Model
{
  //
  public function topupVoucher($member_uuid, $amount, $note, $pic)
  {
    $pic_name = isset($pic->nama) ? $pic->nama : "Admin";
    $user = User::whereHas('member', function ($query) use ($member_uuid) {
      return $query->where('member_uuid', $member_uuid);
    })->first();

    //max voucher joybizer 5jt
    $max = $user->flag > 1 ? 100000000 : 5000000;
    $sisa_amount = 0;

    $now = Carbon::now();
    $sDate = Carbon::now()->startOfMonth();
    $eDate = $now->endOfMonth();

    $note = $note . " by " . $pic_name . " at " . $now;

    $index = VoucherDetail::whereBetween(
      'created_at',
      DB::raw('created_at::date'),
      [$sDate, $eDate]
    ) //('created_at', [$sDate, $eDate])
      ->count() + 1;
    $code = "VCI" . $now->year . $now->format('m') . $index;

    $voucher = Voucher::firstOrCreate(['member_uuid' => $member_uuid], ['saldo' => encrypt(0)]);

    $saldo = decrypt($voucher->saldo);
    $new_saldo = $amount + $saldo;

    if ($saldo <= $max) {
      $max_amount = $max - $saldo;
      $new_amount = $amount;

      if ($amount > $max_amount) {
        $new_amount = $max_amount;
        $sisa_amount = $amount - $max_amount;
      }

      $new_saldo = $new_amount + $saldo;

      $result = VoucherDetail::create([
        'member_uuid' => $member_uuid,
        'code' => $code,
        'debit' => encrypt($new_amount),
        'credit' => encrypt(0),
        'note' => $note
      ]);
      $voucher->saldo = encrypt($new_saldo);
      $voucher->save();
    } else {
      $sisa_amount = $amount;
    }

    if ($sisa_amount > 0) {
      $note = "Topup Voucher Limit mencapai max oleh " . $pic_name . " pada tanggal " . $now;
      $this->topupWallet($member_uuid, $sisa_amount, 3, $note);
    }

    return $code;
  }

  public function topupWallet($member_uuid, $amount, $mode, $note)
  {

    $now = Carbon::now();
    $sDate = Carbon::now()->startOfMonth();
    $eDate = $now->endOfMonth();

    $index = WalletDetail::whereBetween(
      'created_at',
      DB::raw('created_at::date'),
      [$sDate, $eDate]
    ) //('created_at', [$sDate, $eDate])
      ->count() + 1;
    $code = "WCI" . $mode . $now->year . $now->format('m') . $index;

    $wallet = Wallet::firstOrCreate(['member_uuid' => $member_uuid], ['saldo' => encrypt(0)]);

    $saldo = decrypt($wallet->saldo);
    $new_saldo = $amount + $saldo;


    $result = WalletDetail::create(
      [
        'member_uuid' => $member_uuid,
        'code' => $code,
        'debit' => encrypt($amount),
        'credit' => encrypt(0),
        'note' => $note
      ]
    );

    $wallet->saldo = encrypt($new_saldo);
    $wallet->save();

    return true;
  }

  public function setSaldoVoucher($member_uuid, $amount)
  {
    $voucher = Voucher::where('member_uuid', $member_uuid)->first();
    $voucher->saldo = encrypt($amount);
    $voucher->save();
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

  function updateVoucherDetail($code, $debit, $credit)
  {
    $voucher_detail = VoucherDetail::where('code', $code)->first();
    if ($voucher_detail) {
      $voucher_detail->debit = encrypt($debit);
      $voucher_detail->credit = encrypt($credit);
      $voucher_detail->save();
      return true;
    } else {
      return false;
    }
  }

  public function topupVoucherJoyplan($member_uuid, $amount, $note, $pic)
  {

    $pic_name = isset($pic->nama) ? $pic->nama : "Admin";
    $user = User::whereHas('member', function ($query) use ($member_uuid) {
      return $query->where('member_uuid', $member_uuid);
    })->first();

    $now = Carbon::now();
    $sDate = Carbon::now()->startOfMonth();
    $eDate = $now->endOfMonth();

    $note = $note . " by " . $pic_name . " at " . $now;

    $index = WalletDetail::whereBetween(
      'created_at',
      DB::raw('created_at::date'),
      [$sDate, $eDate]
    ) //('created_at', [$sDate, $eDate])
      ->count() + 1;

    $code = "VCI" . $now->year . $now->format('m') . $index;

    $voucher = Voucher::firstOrCreate(['member_uuid' => $member_uuid], ['saldo' => encrypt(0)]);

    $saldo = decrypt($voucher->saldo);
    $new_saldo = $amount + $saldo;

    $result = VoucherDetail::create(['member_uuid' => $member_uuid, 'code' => $code, 'debit' => encrypt($amount), 'credit' => encrypt(0), 'note' => $note]);
    $voucher->saldo = encrypt($new_saldo);
    $voucher->save();

    return $result;
  }

  public function voucherWillExpired($username)
  {
    $user = User::with('member')->where('email', $username)->first();

    $sdate = Carbon::now()->addDay()->subMonths(11)->toDateString();
    $edate = Carbon::now()->toDateString();

    $voucher = Voucher::where('member_uuid', $user->member->uuid)->first();

    if ($voucher) {
      $details = VoucherDetail::where('member_uuid', $voucher->member_uuid)
        ->whereBetween(
          'created_at',
          DB::raw('created_at::date'),
          [$sdate, $edate]
        )
        // [$sdate . " 00:00:00", $edate . " 23:59:59"])
        ->get();

      $total = 0;
      foreach ($details as $key => $detail) {
        $total += decrypt($detail->debit);
      }

      $saldo = decrypt($voucher->saldo);
      if ($saldo > $total) {
        $exp = $saldo - $total;
        return $exp;
      }
    }

    return 0;
  }

  public function getSaldoVoucher($member_uuid)
  {
    $voucher = Voucher::where('member_uuid', $member_uuid)->first();
    $saldo = decrypt($voucher->saldo);
    return $saldo;
  }

  public function money($type, $member_uuid, $amount, $credit, $description, $freeze = false, $transaction_code = null)
  {
    $prefix = $credit ? "C" : "D";
    $code = $prefix . Carbon::now()->format('YmdHis') . rand(1000, 9999);

    try {
      DB::beginTransaction();

      if ($type == "cashback") {
        $wallet = VoucherCashback::firstOrCreate([
          'member_uuid' => $member_uuid,
          'uuid' => Str::uuid()
        ]);
        $detail = VoucherCashbackDetail::create([
          'member_uuid' => $member_uuid,
          'uuid' => Str::uuid(),
          'code' => $code
        ]);
      }

      if ($detail) {
        $detail->credit = $credit;
        $detail->amount = $amount;
        $detail->encrypted_amount = encrypt($amount);
        $detail->description = $description;
        $detail->transaction_code = $transaction_code;
        $detail->save();
      }

      $total_amount = ($wallet->encrypted_amount ? decrypt($wallet->encrypted_amount) : 0);
      $total_amount = $credit ? $total_amount - $amount : $total_amount + $amount;

      $wallet->amount = $total_amount;
      $wallet->encrypted_amount = encrypt($total_amount);
      $wallet->save();

      DB::commit();

      return $wallet;
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }
}
