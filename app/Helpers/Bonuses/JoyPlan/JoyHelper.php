<?php

namespace App\Helpers\Bonuses;

use App\Models\Bonuses\Joys\JoyCarryForward;
use App\Models\Bonuses\Joys\JoyData;
use App\Models\Bonuses\Joys\JoyPointReward;
use App\Models\Bonuses\Joys\JoyRVForward;
use App\Models\Bonuses\Ranks\SRank;
use App\Models\Bonuses\VitalSign;
use App\Models\Members\Member;
use App\Models\Orders\Production\OrderHeader;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JoyHelper extends Model
{
  //    
  public function syncJoyData($date)
  {
    Log::info('Sycn Joy Data ' . $date . ' at ' . Carbon::now());
    $transactions = OrderHeader::where('transaction_date', $date)
      ->with('user', 'member')
      ->get();
    foreach ($transactions as $key => $t) {
      echo $key;
      Log::debug($t->code_trans);
      Log::debug("-------------");
      if ($t->total_bv > 0 || $t->total_rv > 0) {
        $user = Member::where('uuid', $t->member_uuid)
          ->with('srank', 'user')
          ->first();
        Log::debug($user->user->uuid);
        if (isset($user->srank) && $user->srank->appv >= 2400) {
          $jrank = 3;
        } elseif (isset($user->srank) && $user->srank->appv >= 1200) {
          $jrank = 2;
        } elseif (isset($user->srank) && $user->srank->appv >= 240) {
          $jrank = 1;
        } else {
          $jrank = 0;
        }

        $joydata = JoyData::firstOrCreate([
          'date' => $t->transaction_date,
          'member_uuid' => $user->uuid
        ]);
        $joydata->sponsor_uuid = $user->sponsor_uuid;
        $joydata->placement_uuid = $user->placement_uuid;

        $joydata->ppv += $t->total_pv_plan_joy;
        $joydata->pbv += $t->total_bv_plan_joy;
        $joydata->prv += $t->total_rv_plan_joy;

        $joydata->pgpv += $t->total_pv_plan_joy;
        $joydata->pgbv += $t->total_bv_plan_joy;
        $joydata->pgrv += $t->total_rv_plan_joy;

        $joydata->jrank = $jrank;
        $joydata->save();

        Log::debug("-----------");
        if (!is_null($t->membership->placement_uuid)) {
          $this->pushJoyData(
            $t->transaction_date,
            $t->membership->placement_uuid,
            $t->total_pv_plan_joy,
            $t->total_bv_plan_joy,
            $t->total_rv
          );
        }
        Log::debug("-----------");
      }
    }
    Log::info('Sycn Joy Data ' . $date . 'finish at ' . Carbon::now());
  }

  public function syncJoyDatabyCode($uuid)
  {
    // $t = transaksi::where('code_trans', $code)->whereIn('status', ['S', 'A', 'PC', 'I'])->with('user')->first();
    $t = OrderHeader::where('uuid', $uuid)
      ->with('member')
      ->first();
    #foreach ($transactions as $key => $t) {
    #Log::debug($t->code_trans);
    if ($t->total_bv > 0 || $t->total_rv > 0) {
      $user = Member::where('uuid', $t->member_uuid)
        ->with('srank')
        ->first();
      if (isset($user->srank) && $user->srank->appv >= 2400) {
        $jrank = 3;
      } elseif (isset($user->srank) && $user->srank->appv >= 1200) {
        $jrank = 2;
      } elseif (isset($user->srank) && $user->srank->appv >= 240) {
        $jrank = 1;
      } else {
        $jrank = 0;
      }

      $joydata = JoyData::firstOrCreate([
        'date' => $t->transaction_date,
        'member_uuid' => $user->uuid
      ]);
      $joydata->sponsor_uuid = $user->sponsor_uuid;
      $joydata->placement_uuid = $user->placement_uuid;
      $joydata->ppv += $t->total_pv_plan_joy;
      $joydata->pbv += $t->total_bv_plan_joy;
      $joydata->prv += $t->total_rv_plan_joy;

      $joydata->pgpv += $t->total_pv_plan_joy;
      $joydata->pgbv += $t->total_bv_plan_joy;
      $joydata->pgrv += $t->total_rv_plan_joy;

      $joydata->jrank = $jrank;
      $joydata->save();

      if (!is_null($t->member->placement_uuid)) $this->pushJoyData(
        $t->transaction_date,
        $t->member->placement_uuid,
        $t->total_pv_plan_joy,
        $t->total_bv_plan_joy,
        $t->total_rv
      );
    }
  }



  public function pushJoyData($date, $upid, $gpv, $gbv, $grv)
  {
    $user = Member::where('uuid', $upid)->with('srank')->first();
    if ($user) {
      if (isset($user->srank) && $user->srank->appv >= 2400) {
        $jrank = 3;
      } elseif (isset($user->srank) && $user->srank->appv >= 1200) {
        $jrank = 2;
      } elseif (isset($user->srank) && $user->srank->appv >= 240) {
        $jrank = 1;
      } else {
        $jrank = 0;
      }

      $joydata = JoyData::firstOrCreate(['date' => $date, 'member_uuid' => $user->member_uuid]);
      $joydata->sponsor_uuid = $user->sponsor_uuid;
      $joydata->placement_uuid = $user->placement_uuid;
      $joydata->gpv += $gpv;
      $joydata->gbv += $gbv;
      $joydata->grv += $grv;
      $joydata->pgpv += $gpv;
      $joydata->pgbv += $gbv;
      $joydata->pgrv += $grv;
      $joydata->jrank = $jrank;
      $joydata->save();

      if ($user->placement_uuid != null) $this->pushJoyData(
        $date,
        $user->placement_uuid,
        $gpv,
        $gbv,
        $grv
      );
    }
  }

  public function clearJoyData($date)
  {
    echo "Start Clear Data \n";
    JoyData::where('date', $date)
      ->update([
        'ppv' => 0,
        'pbv' => 0,
        'prv' => 0,
        'gpv' => 0,
        'gbv' => 0,
        'grv' => 0,
        'pgpv' => 0,
        'pgbv' => 0,
        'pgrv' => 0
      ]);
    echo "End Clear Data \n";
  }

  public function deleteJoyData($date)
  {
    JoyData::where('date', $date)->delete();
  }

  public function pushNewABGCount($member_uuid, $year, $month, $rank)
  {

    $user = Member::where('uuid', $member_uuid)->first();
    if ($member_uuid && $user) {
      $vs = VitalSign::firstOrCreate([
        'uuid' => Str::uuid()->toString(),
        'member_uuid' => $user->uuid,
        'year' => $year,
        'month' => $month
      ]);
      $vs->abg++;

      if ($rank == 1) {
        $vs->a++;
      } elseif ($rank == 2) {
        $vs->b++;
      } elseif ($rank >= 3) {
        $vs->g++;
      }

      $vs->save();

      if ($user->placement_uuid) {
        $this->pushNewABGCount($user->placement_uuid, $year, $month, $rank);
      }
    }
  }


  public function checkAtLeg($master, $target, $member_uuid)
  {
    $user = Member::where('uuid', $member_uuid)->first();

    if ($user->placement_uuid == $target) {
      $result = true;
    } elseif ($user->placement_uuid == $master || $user->placement_uuid == null) {
      $result = false;
    } elseif ($user->upiplacement_uuidd != null) {
      $result = $this->checkAtLeg($master, $target, $user->placement_uuid);
    }

    return $result;
  }

  public function checkYoungEagle($uuid, $mid)
  {
    $user = Member::where('uuid', $uuid)
      ->with(['srank', 'vital_signs'])
      ->first();
    $gammaChilds = SRank::where([[
      'sponsor_uuid',
      $user->uuid
    ], ['srank', '>=', 3]])->get();

    $gammaAtLeft = false;
    $gammaAtRight = false;

    foreach ($gammaChilds as $key => $child) {
      $gammaAtLeft = $this->checkAtLeg(
        $user->uuid,
        $user->left,
        $child->user->iuud
      );
      if (!$gammaAtLeft) $gammaAtRight = true;
    }



    if ($user->srank >= 3) {
      $sponsorGamma = SRank::where([
        ['sponsor_uuid', $user->uuid],
        ['srank', 3]
      ])->get();
    }
  }

  public function resetCarryForward($member_uuid)
  {
    $jcf = JoyCarryForward::where('member_uuid', $member_uuid)
      ->orderBy('date', 'DESC')
      ->first();
    if ($jcf) {
      $jcf->big_bv = 0;
      $jcf->small_bv = 0;
      $jcf->save();
    }

    $jrv = JoyRVForward::where('member_uuid', $member_uuid)
      ->orderBy('date', 'DESC')
      ->first();
    if ($jrv) {
      $jrv->big_rv = 0;
      $jrv->small_rv = 0;
      $jrv->save();
    }

    $jpr = JoyPointReward::where('member_uuid', $member_uuid)->delete();
  }

  public function diffMonth($sdate, $edate)
  {
    $to = Carbon::parse($sdate);
    $from = Carbon::parse($edate);
    return $to->diffInMonths($from);
  }

  public function dormant($uuid)
  {
    $member = Member::where('uuid', $uuid)->with(['srank'])->first();
    $now = Carbon::now()->toDateString();

    if ($member->status == 3) { // 3 = status member is Dormant
      if ($now == $member->will_dormant_at) {
        $member->status = 3;

        foreach ($member as $membership) {
          $membership->will_dormant_at = $member->will_dormant_at;
          $membership->save();

          $this->resetCarryForward($membership->uuid);
        }
      } elseif ($member->will_dormant_at > $now && $member->status == 3) {
        $member->status = 1;
        $member->save();

        foreach ($member as $membership) {
          $membership->status = 1;
        }
      } elseif ($member->will_dormant_at  < $now && $member->status == 1) {
        $lastOrder = OrderHeader::where('member_uuid', $member->uuid)
          ->orderBy('created_at', 'DESC')
          ->first();
        $will_dormant_at = Carbon::parse($lastOrder->transaction_date)->addMonths(6)->toDateString();

        $member->will_dormant_at = $will_dormant_at;

        foreach ($member as $membership) {
          $membership->will_dormant_at = $will_dormant_at;

          $this->resetCarryForward($membership->uuid);
        }
      }
      $member->save();
    }
  }

  public function downgradeToSC($uuid)
  {

    try {
      DB::beginTransaction();
      $u = User::where('uuid', $uuid)->with('member')->first();

      if ($u) {
        $email = $u->email;
        $username = $u->username;
        $first_name = $u->first_name;
        $last_name = $u->last_name;
        $password = $u->password;

        $u->password = "1266hwehfsweo34iu437797dhfkjsdhfshf";
        $u->username = "dgsc-" . $u->username;
        $u->email = "dgsc-" . $u->email;
        $u->status = 0;
        $u->save();

        $newUser = $u->replicate()->fill([
          'uid' => Str::uuid(),
          'first_name' => $first_name,
          'last_name' => $last_name,
          'username' => $username,
          'password' => $password,
          'email' => $email,
          'status' => 1,
        ]);
        $newUser->save();
      }


      #membership
      $m = Member::where('uuid', $u->member->uuid)->first();
      $username = $m->username;
      Member::where('sponsor_uuid', $m->uuid)
        ->update([
          'sponsor_uuid' => $m->sponsor_uuid
        ]);

      $newMembership = $m->replicate()->fill([
        'uid' => Str::uuid(),
        'user_id' => $newUser->id,
        'membership_status' => 2,
        'status' => 1,
        'min_bv' => 0,
        'sponsor_uuid' => null,
        'sponsor_id' => null,
        'placement_uuid' => null,
        'placement_id' => null,
      ]);

      $newMembership->save();

      DB::commit();
    } catch (\Throwable $th) {
      DB::rollBack();
      echo $th;
    }
  }
}
