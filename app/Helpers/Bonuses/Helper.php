<?php

namespace App\Helpers\Bonuses;

use app\Libraries\Core;
use App\Models\Bonuses\BonusRankLog;
use App\Models\Bonuses\BonusWeekly;
use App\Models\Bonuses\CarryForwardDetail;
use App\Models\Bonuses\CouponsAndRewards\MonthlyRewardCoupon;
use App\Models\Bonuses\CouponsAndRewards\Voucher;
use App\Models\Bonuses\CouponsAndRewards\VoucherCashback;
use App\Models\Bonuses\CouponsAndRewards\VoucherCashbackDetail;
use App\Models\Bonuses\CouponsAndRewards\VoucherDetail;
use App\Models\Bonuses\Joys\JoyBonusSummary;
use App\Models\Bonuses\Joys\JoyPointReward;
use App\Models\Bonuses\PreparedDatas\Joy as PreparedDataJoy;
use App\Models\Bonuses\PreparedDatas\PreparedDataBiz;
use App\Models\Bonuses\Ranks\ERank;
use App\Models\Bonuses\Ranks\SRank;
use App\Models\Calculations\Bonuses\Period;
use App\Models\Members\Member;
use App\Models\Orders\Production\OrderDetail;
use App\Models\Orders\Production\OrderHeader;
use App\Models\Users\User;
use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Helper extends Model
{


  // public function GenerateCoupon($lid, $owner, $uuid)
  // {

  //   $lid = 1;
  //   $owner = $owner;
  //   $code_trans = $uuid;

  //   do {
  //     $coupon = $this->RandomAlphaNumeric(5);
  //     $unique = LotteryCoupon::where('coupon', $coupon)->first();
  //   } while ($unique);


  //   $Coupon = LotteryCoupon::create(['lid' => $lid, 'owner' => $owner, 'coupon' => $coupon, 'code_trans' => $code_trans]);
  // }

  public function RandomAlphaNumeric($max)
  {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    $code = '';
    $characters_size = strlen($characters) - 1;
    for ($i = 0; $i < $max; $i++) {
      $code .= $characters[mt_rand(0, $characters_size)];
    }

    return $code;
  }

  public function check_Rank($apbv, $leg_jbp, $bj, $vj, $user)
  {

    $srank = SRank::where('member_uuid', $user->uuid)->first();
    $srank = isset($srank) ? $srank->srank : 0;
    $childs = SRank::where('sponsor_uuid', $user->uuid)->get();

    $joybonus = JoyBonusSummary::where('member_uuid', $user->uuid)->sum('total');
    $bizbonus = BonusWeekly::where('member_uuid', $user->uuid)->sum('total');
    $ab = $joybonus + $bizbonus; #akumulasi bonus

    $reward = JoyPointReward::where('member_uuid', $user->uuid)->get(); #reward
    $r = $reward->sum('joy') + $reward->sum('biz');

    $sAlpha = 0;
    $sBeta = 0;
    $sGamma = 0;
    $sJPS = 0;
    $sJP = 0;
    $rank = 0;

    foreach ($childs as $key => $child) {
      # code...
      if ($child->srank >= 5) {
        $sJP++;
      } elseif ($child->srank == 4) {
        $sJPS++;
      } elseif ($child->srank == 3) {
        $sGamma++;
      } elseif ($child->srank == 2) {
        $sBeta++;
      } elseif ($child->srank == 1) {
        $sAlpha++;
      }
    }

    $sGamma = $sGamma + $sJPS + $sJP;

    $legs = SRank::where('placement_uuid', $user->uuid)->get();
    $leg1 = isset($legs[0]) ? $legs[0] : null;
    $leg2 = isset($legs[1]) ? $legs[1] : null;

    if ($apbv < 2400) {
      if ($apbv < 240) {
        $rank = 0;
      } elseif ($apbv < 1200) {
        $rank = 1;
      } else {
        $rank = 2;
      }
    } else {
      if ($srank >= 6 && $leg_jbp >= 2) {
        if ($leg1->vj >= 16 && $leg2->vj >= 16 && $r >= 65536 && $ab >= 2000000000) {
          if ($srank < 13 && $leg1->vj_active >= 16 && $leg2->vj_active >= 16) {
            $rank = 13;
          }
        } elseif ($leg1->vj >= 8 && $leg2->vj >= 8  && $ab >= 1000000000) {
          if ($srank < 12 && $leg1->vj_active >= 8 && $leg2->vj_active >= 8) {
            $rank = 12;
          }
        } elseif ($leg1->vj >= 6 && $leg2->vj >= 6 && $ab >= 500000000) {
          if ($srank < 11 && $leg1->vj_active >= 6 && $leg2->vj_active >= 6) {
            $rank = 11;
          }
        } elseif ($leg1->vj >= 3 && $leg2->vj >= 3 && $ab >= 250000000) {
          if ($srank < 10 && $leg1->vj_active >= 3 && $leg2->vj_active >= 3) {
            $rank = 10;
          }
        } elseif ($leg1->vj >= 1 && $leg2->vj >= 1  && $ab >= 150000000) {
          if ($srank < 9 && $leg1->vj_active >= 1 && $leg2->vj_active >= 1) {
            $rank = 9;
          }
        } elseif ($leg1->bj >= 1 && $leg2->bj >= 1 && $ab >= 50000000) {
          if ($srank < 8 && $leg1->bj_active >= 1 && $leg2->bj_active >= 1) {
            $rank = 8;
          }
        } elseif ($ab >= 25000000) {
          $rank = 7;
        } else {
          $rank = 6;
        }
      } else {
        if (($sGamma + $sBeta + $sAlpha) >= 10 && $sJP >= 2) {
          $rank = 6;
        } elseif (($sGamma + $sBeta + $sAlpha) >= 5 && ($sJPS + $sJP) >= 2) {
          $rank = 5;
        } elseif (($sGamma) >= 2) {
          $rank = 4;
        } else {
          $rank = 3;
        }
      }
    }

    return $rank;
  }



  public function check_eRank($member_uuid, $pbv, $mid)
  {
    $srank = SRank::where('member_uuid', $member_uuid)->first();
    $rank = 0;


    if ($pbv < 800) {
      if ($pbv < 96) {
        $rank = 0;
      } elseif ($pbv < 240) {
        $rank = 3;
      } elseif ($pbv < 480) {
        $rank = 4;
      } elseif ($pbv < 800) {
        $rank = 5;
      }
    } else {

      $legs = ERank::where('placement_uuid', $member_uuid)->orderBy('gpv', 'DESC')->get();
      $leg1 = isset($legs[0]) ? $legs[0] : null;
      $leg2 = isset($legs[1]) ? $legs[1] : null;

      $rank = 6;

      if ($legs->count() >= 2) {
        $pgbv1 = $leg1->ppv + $leg1->gpv;
        $pgbv2 = $leg2->ppv + $leg2->gpv + $pbv;

        if ($pgbv1 >= 160000 && $pgbv2 >= 160000) {
          $rank = 13;
        } elseif ($pgbv1 >= 80000 && $pgbv2 >= 80000) {
          $rank = 12;
        } elseif ($pgbv1 >= 32000 && $pgbv2 >= 32000) {
          $rank = 11;
        } elseif ($pgbv1 >= 20000 && $pgbv2 >= 20000) {
          $rank = 10;
        } elseif ($pgbv1 >= 16000 && $pgbv2 >= 16000) {
          $rank = 9;
        } elseif ($pgbv1 >= 8000 && $pgbv2 >= 8000) {
          $rank = 8;
        } elseif ($pgbv1 >= 800 && $pgbv2 >= 800) {
          $rank = 7;
        }
      }
    }

    //if effective rank larger then status rank, then set rank with status rank
    if ($rank > $srank->srank) $rank = $srank->srank;
    return $rank;
  }

  //update Joy & Biz Plan requirement data
  public function resyncPlanJoy(
    $member_uuid,
    $ppv,
    $pbv,
    $gpv,
    $gbv,
    $updated_at,
    $jbp,
    $bj,
    $vj,
    $ppvj,
    $pbvj,
    $gpvj,
    $gbvj,
    $ppvb,
    $pbvb,
    $gpvb,
    $gbvb,
    $omzet,
    $ozj,
    $ozb,
    $opc,
    $prvj,
    $grvj
  ) {
    $user = Member::select('member_uuid', 'username', 'sponsor_uuid', 'placement_uuid', 'uuid')
      ->where('uuid', $member_uuid)
      ->first();

    if ($user) {

      $spid = $user->sponsor_uuid;
      $upid = $user->upline_uuid;

      $wid = Period::where([
        ['start_date', '<=', $updated_at],
        ['end_date', '>=', $updated_at]
      ])->first(['id', 'start_date', 'end_date']);
      $mid = date('Ym', strtotime($wid->eDate));

      /* update status rank */
      $srank = SRank::updateOrCreate(
        ['member_uuid' => $member_uuid],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );
      $leg_jbp = SRank::where([
        ['placement_uuid', $member_uuid],
        ['jbp', '>', 0]
      ])->orWhere([
        ['placement_uuid', $member_uuid],
        ['srank', 5]
      ])->count();
      $current_rank = 0;

      if (is_null($srank->srank)) {
        $srank->srank = 0;
        $srank->save();
      } else {
        $current_rank = $srank->srank;
      }


      $srank_downlines = SRank::where('sponsor_uuid', $member_uuid)->get();

      $tot_bj = 0;
      $tot_vj = 0;

      $tot_vj_temp = 0;
      $tot_bj_temp = 0;


      foreach ($srank_downlines as $srank_downline) {
        if ($srank_downline->srank == 6) {
          $tot_bj_temp = 1 + $srank_downline->bj;
        } else {
          $tot_bj_temp = $srank_downline->bj;
        }

        if ($srank_downline->srank >= 7) {
          $tot_vj_temp = 1 + $srank_downline->vj;
        } else {
          $tot_vj_temp = $srank_downline->vj;
        }


        $tot_vj += ($tot_vj_temp > 3) ? 3 : $tot_vj_temp;
        $tot_bj += ($tot_bj_temp > 3) ? 3 : $tot_bj_temp;
      }

      $newRank = $this->check_Rank($srank->appv, $leg_jbp, $tot_bj, $tot_vj, $user);


      $srank->jbp += $jbp;
      $srank->bj += $bj;
      $srank->vj += $vj;
      $srank->srank = $newRank > $srank->srank ? $newRank : $srank->srank;

      $srank->save();

      if ($newRank > $current_rank) {
        $result = BonusRankLog::firstOrCreate([
          'uuid' => Str::uuid(),
          'process_uuid' => null,
          'member_uuid' => $member_uuid,
          'rank_id' => $newRank,
          // 'rank_uuid' => $newRank,
          'created_at' => $updated_at
        ]);
      }

      if (($newRank >= 6) && ($newRank > $current_rank)) {
        switch ($current_rank) {
          case 6:
            $jbp -= 1;
            break;
          case 7:
            $bj -= 1;
            break;
        }

        switch ($newRank) {
          case 6:
            $jbp += 1;
            break;
          case 7:
            $bj += 1;
            break;
          case 8:
            $vj += 1;
            break;
        }
      }

      /* update status rank done */

      $erank = ERank::updateOrCreate(
        ['member_uuid' => $member_uuid, 'mid' => $mid],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );

      $current_erank = is_null($erank->erank) ? 0 : $erank->erank;

      $erank->gpv += $gpv;

      $erank->erank = $this->check_eRank($member_uuid, $erank->ppv, $mid);
      $erank->save();


      $preparedDataJoy = PreparedDataJoy::firstOrCreate(
        ['member_uuid' => $member_uuid, 'wid' => $wid->id],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );

      $preparedDataJoy->ppv += $ppv;
      $preparedDataJoy->pbv += $pbv;

      $preparedDataJoy->ppvj += $ppvj;
      $preparedDataJoy->pbvj += $pbvj;
      $preparedDataJoy->prvj += $prvj;

      $preparedDataJoy->gpvj += $gpvj;
      $preparedDataJoy->gbvj += $gbvj;
      $preparedDataJoy->grvj += $grvj;

      $preparedDataJoy->omzet += $omzet;
      $preparedDataJoy->ozj += $ozb;
      $preparedDataJoy->srank = $srank->srank;
      $preparedDataJoy->erank = $erank->erank;
      $preparedDataJoy->opc += $opc;
      $preparedDataJoy->save();

      /* :D */
      $ppv  += $gpv;
      $pbv  += $gbv;
      $ppvj += $gpvj;
      $pbvj += $gbvj;
      $ppvb = 0;
      $pbvb = 0;
      $prvj += $grvj;
      /* :D */

      if ($user->placement_uuid) {
        $this->resyncPlanJoy(
          $upid,
          0,
          0,
          $ppv,
          $pbv,
          $updated_at,
          $jbp,
          $bj,
          $vj,
          0,
          0,
          $ppvj,
          $pbvj,
          0,
          0,
          $ppvb,
          $pbvb,
          $omzet,
          $ozj,
          $ozb,
          $opc,
          0,
          $prvj
        );
      }
    }
  }

  //update Joy & Biz Plan requirement data
  public function resyncPlanBiz(
    $member_uuid,
    $ppv,
    $pbv,
    $gpv,
    $gbv,
    $updated_at,
    $jbp,
    $bj,
    $vj,
    $ppvj,
    $pbvj,
    $gpvj,
    $gbvj,
    $ppvb,
    $pbvb,
    $gpvb,
    $gbvb,
    $omzet,
    $ozj,
    $ozb
  ) {
    $user = Member::select('id', 'uuid', 'sponsor_uuid', 'placement_uuid')
      ->where('uuid', $member_uuid)
      ->first();

    if ($user) {
      $spid = $user->sponsor_uuid;
      $upid = $user->placement_uuid;

      $wid = Period::where([
        ['start_date', '<=', $updated_at],
        ['end_date', '>=', $updated_at]
      ])->first();
      $mid = date('Ym', strtotime($wid->eDate));

      /* update status rank */
      $srank = SRank::updateOrCreate(
        ['member_uuid' => $member_uuid],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );
      $leg_jbp = SRank::where([
        ['placement_uuid', $member_uuid],
        ['jbp', '>', 0]
      ])->orWhere([['placement_uuid', $member_uuid], ['srank', 5]])
        ->count();
      $current_rank = is_null($srank->srank) ? 0 : $srank->srank;

      $srank_downlines = SRank::where('placement_uuid', $member_uuid)->get();
      $tot_bj = 0;
      $tot_vj = 0;

      $tot_vj_temp = 0;
      $tot_bj_temp = 0;

      foreach ($srank_downlines as $srank_downline) {
        if ($srank_downline->srank == 6) {
          $tot_bj_temp = 1 + $srank_downline->bj;
        } else {
          $tot_bj_temp = $srank_downline->bj;
        }

        if ($srank_downline->srank >= 7) {
          $tot_vj_temp = 1 + $srank_downline->vj;
        } else {
          $tot_vj_temp = $srank_downline->vj;
        }


        $tot_vj += ($tot_vj_temp > 3) ? 3 : $tot_vj_temp;
        $tot_bj += ($tot_bj_temp > 3) ? 3 : $tot_bj_temp;
      }

      $newRank = $this->check_Rank($srank->appv, $leg_jbp, $tot_bj, $tot_vj, $user);
      $tempSrank = $newRank > $srank->srank ? $newRank : $srank->srank;

      $srank->jbp += $jbp;
      $srank->bj += $bj;
      $srank->vj += $vj;
      $srank->srank = $tempSrank ? $tempSrank : 0;
      $srank->save();

      if ($newRank > $current_rank) {
        $result = BonusRankLog::firstOrCreate([
          'uuid' => Str::uuid(),
          'process_uuid' => null,
          'member_uuid' => $member_uuid,
          'rank_id' => $newRank,
          // 'rank_uuid' => $newRank,
          'created_at' => $updated_at
        ]);
      }


      if (($newRank >= 5) && ($newRank > $current_rank)) {
        switch ($current_rank) {
          case 5:
            $jbp -= 1;
            break;
          case 6:
            $bj -= 1;
            break;
        }

        switch ($newRank) {
          case 5:
            $jbp += 1;
            break;
          case 6:
            $bj += 1;
            break;
          case 7:
            $vj += 1;
            break;
        }
      }

      /* update status rank done */

      $erank = ERank::updateOrCreate(
        ['member_uuid' => $member_uuid, 'mid' => $mid],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );
      $current_erank = is_null($erank->erank) ? 0 : $erank->erank;
      $erank->gpv += $gpv;
      $erank->erank = $this->check_eRank($member_uuid, $erank->ppv, $mid);
      $erank->save();


      //if ppvb or gpvb not null
      if ($ppvb || $gpvb) {
        $preparedDataBiz = PreparedDataBiz::firstOrCreate(
          ['member_uuid' => $member_uuid, 'mid' => $mid],
          ['spid' => $spid, 'upid' => $upid]
        );
        $preparedDataBiz->ppv += $ppv;
        $preparedDataBiz->pbv += $pbv;
        $preparedDataBiz->gpv += $gpv;
        $preparedDataBiz->gbv += $gbv;
        $preparedDataBiz->ppvb += $ppvb;
        $preparedDataBiz->pbvb += $pbvb;
        $preparedDataBiz->gpvb += $gpvb;
        $preparedDataBiz->gbvb += $gbvb;
        $preparedDataBiz->omzet += $omzet;
        $preparedDataBiz->ozb += $ozb;
        $preparedDataBiz->srank = $srank->srank;
        $preparedDataBiz->erank = $erank->erank;
        $preparedDataBiz->save();
      }

      /* :D */
      $ppv  += $gpv;
      $pbv  += $gbv;
      $ppvj += $gpvj;
      $pbvj += $gbvj;
      $ppvb += $gpvb;
      $pbvb += $gbvb;
      /* :D */

      $this->resyncPlanBiz(
        $upid,
        0,
        0,
        $ppv,
        $pbv,
        $updated_at,
        $jbp,
        $bj,
        $vj,
        0,
        0,
        $ppvj,
        $pbvj,
        0,
        0,
        $ppvb,
        $pbvb,
        $omzet,
        $ozj,
        $ozb
      );
    }
  }


  public function update_Effective_Rank_Sim(
    $member_uuid,
    $ppv,
    $pbv,
    $gpv,
    $gbv,
    $updated_at,
    $jbp,
    $bj,
    $vj,
    $ppvj,
    $pbvj,
    $gpvj,
    $gbvj,
    $ppvb,
    $pbvb,
    $gpvb,
    $gbvb,
    $omzet,
    $ozj,
    $ozb,
    $opc,
    $prvj,
    $grvj,
    $prv,
    $grv
  ) {
    $user = Member::select('id', 'uuid', 'sponsor_uuid', 'placement_uuid')
      ->where('member_uuid', $member_uuid)
      ->first();

    //Log::info($user->username);
    if ($user) {
      $spid = $user->sponsor_uuid;
      $upid = $user->placement_uuid;

      $wid = Period::where([
        ['start_date', '<=', $updated_at],
        ['end_date', '>=', $updated_at]
      ])->first(['id', 'start_date', 'end_date']);
      $mid = date('Ym', strtotime($wid->eDate));

      /* update status rank */
      $srank = SRank::firstOrCreate(
        ['member_uuid' => $member_uuid],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );
      $leg_jbp = SRank::where([
        ['placement_uuid', $member_uuid],
        ['jbp', '>', 0]
      ])->orWhere([
        ['placement_uuid', $member_uuid],
        ['srank', 5]
      ])->count();
      $current_rank = is_null($srank->srank) ? 0 : $srank->srank;

      $srank_downlines = SRank::where('placement_uuid', $member_uuid)->get();
      $tot_bj = 0;
      $tot_vj = 0;

      $tot_vj_temp = 0;
      $tot_bj_temp = 0;

      foreach ($srank_downlines as $srank_downline) {
        if ($srank_downline->srank == 6) {
          $tot_bj_temp = 1 + $srank_downline->bj;
        } else {
          $tot_bj_temp = $srank_downline->bj;
        }

        if ($srank_downline->srank >= 7) {
          $tot_vj_temp = 1 + $srank_downline->vj;
        } else {
          $tot_vj_temp = $srank_downline->vj;
        }

        $tot_vj += ($tot_vj_temp > 3) ? 3 : $tot_vj_temp;
        $tot_bj += ($tot_bj_temp > 3) ? 3 : $tot_bj_temp;
      }

      $appv = $srank ? $srank->appv : 0;
      $apbv = $srank ? $srank->apbv : 0;

      $newRank = $this->check_Rank($appv + $ppv, $leg_jbp, $tot_bj, $tot_vj, $user);
      $tempSrank = $newRank > $srank->srank ? $newRank : $srank->srank;

      $srank->appv += $ppv;
      $srank->apbv += $pbv;
      $srank->jbp += $jbp;
      $srank->bj += $bj;
      $srank->vj += $vj;
      $srank->srank = $tempSrank ? $tempSrank : 0;
      $srank->save();

      if ($newRank > $current_rank) {
        $result = BonusRankLog::firstOrCreate([
          'uuid' => Str::uuid(),
          'process_uuid' => null,
          'member_uuid' => $member_uuid,
          'rank_id' => $newRank,
          // 'rank_uuid' => $newRank,
          'created_at' => $updated_at
        ]);
      }

      if (($newRank >= 5) && ($newRank > $current_rank)) {
        switch ($current_rank) {
          case 5:
            $jbp -= 1;
            break;
          case 6:
            $bj -= 1;
            break;
        }

        switch ($newRank) {
          case 5:
            $jbp += 1;
            break;
          case 6:
            $bj += 1;
            break;
          case 7:
            $vj += 1;
            break;
        }
      }

      /* update status rank done */

      $erank = ERank::firstOrCreate(
        ['member_uuid' => $member_uuid, 'mid' => $mid],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );
      $current_erank = is_null($erank->erank) ? 0 : $erank->erank;

      $erank->ppv += $ppv;

      $erank->gpv += $gbv;
      $erank->erank = $this->check_eRank($member_uuid, $erank->ppv + $ppv, $mid);
      $erank->save();


      $preparedDataJoy = PreparedDataJoy::firstOrCreate(
        ['member_uuid' => $member_uuid, 'wid' => $wid->id],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );

      $preparedDataJoy->ppv += $ppv;
      $preparedDataJoy->pbv += $pbv;
      $preparedDataJoy->prv += $prv;
      $preparedDataJoy->ppvj += $ppvj;
      $preparedDataJoy->pbvj += $pbvj;
      $preparedDataJoy->gpvj += $gpvj;
      $preparedDataJoy->gbvj += $gbvj;

      $preparedDataJoy->prvj += $prvj;
      $preparedDataJoy->grvj += $grvj;


      $preparedDataJoy->omzet += $omzet;
      $preparedDataJoy->ozj += $ozj;
      $preparedDataJoy->srank = $srank->srank;
      $preparedDataJoy->erank = $erank->erank;
      $preparedDataJoy->opc += $opc;
      $preparedDataJoy->save();

      //if ppvb or gpvb not null
      if ($ppvb || $gpvb) {
        $preparedDataBiz = PreparedDataBiz::firstOrCreate(
          ['member_uuid' => $member_uuid, 'wid' => $wid->id],
          ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
        );
        $preparedDataBiz->ppv += $ppv;
        $preparedDataBiz->pbv += $pbv;
        $preparedDataBiz->gpv += $gpv;
        $preparedDataBiz->gbv += $gbv;
        $preparedDataBiz->ppvb += $ppvb;
        $preparedDataBiz->pbvb += $pbvb;
        $preparedDataBiz->gpvb += $gpvb;
        $preparedDataBiz->gbvb += $gbvb;
        $preparedDataBiz->omzet += $omzet;
        $preparedDataBiz->ozb += $ozb;
        $preparedDataBiz->srank = $srank->srank;
        $preparedDataBiz->erank = $erank->erank;
        $preparedDataBiz->save();
      }

      /* :D */
      $ppv  += $gpv;
      $pbv  += $gbv;
      $prv += $grv;

      $ppvj += $gpvj;
      $pbvj += $gbvj;
      $prvj += $grvj;

      $ppvb += $gpvb;
      $pbvb += $gbvb;
      /* :D */

      if ($upid) $this->update_Effective_Rank_Sim(
        $upid,
        0,
        0,
        $ppv,
        $pbv,
        $updated_at,
        $jbp,
        $bj,
        $vj,
        0,
        0,
        $ppvj,
        $pbvj,
        0,
        0,
        $ppvb,
        $pbvb,
        $omzet,
        $ozj,
        $ozb,
        $opc,
        0,
        $prvj,
        0,
        $prv
      );
    }
  }

  //fuction to update transaction if payment valid
  public function confirmPaymentValid($uuid, $date)
  {
    $money = new Money;
    $joyhelper = new JoyHelper;

    $userlogin = null;
    if (Auth::check()) {
        $user = Auth::user();
        $userlogin = $user->uuid;
    }

    $trx = OrderHeader::where('uuid', $uuid)
      ->with(['member.effectiveRank', 'details.productPrice.product'])
      ->first();

    /*joybiz v1
		//paket Registrasi SC yang mengandung biaya reg dan selisih
		$scRegisterSpecialCase = array('RSC01','RSC02');
		$scRegisterSpecialCase2 = array('RSC04');
		*/

    if ($trx) {
      $trx->transaction_date = $date;
      $trx->save();

      if ($trx->member_uuid) {
        $user = Member::where('uuid', $trx->member_uuid)->with(['sponsor', 'srank'])->first();
      } else {
        $user = Member::where('uuid', $trx->member_uuid)->with(['sponsor', 'srank'])->first();
      }

      $membership = Member::where('uuid', $trx->member_uuid)->first();

      $srank = isset($user->srank) ? $user->srank : null;

      $indent = false;
      $totalHargaWIB = 0;
      $selisihRetail = 0;
      $hasRegister = false;
      // $qudu = 0;
      // $quduBVG = 0;
      // $hargaAsliCod = 0;

      // foreach ($trx['transaksi_detail'] as $trxd) {
      // 	$product_indent = \App\Models\barang::where([['id', $trxd->id_barang_fk], ['status', 'I']])
      // 		->with('barang_detail')->first();
      // 	$indent = isset($product_indent) ? true : false;

      // 	$product = barang::where('id', $trxd->id_barang_fk)->first();
      // 	if ($trx->cod) $hargaAsliCod += $product->harga_1 * $trxd->qty;

      // 	#if upgrade to joybizer
      // 	if (($product->id == 87 || $product->is_register == 1) && $user->flag == 2) {
      // 		$user->flag = 1;
      // 		$user->activated_at = $trx->transaction_date;
      // 		$user->save();

      // 		$trx->id_cust_fk = $trx->id_sc_fk;
      // 		$trx->id_sc_fk = null;
      // 		$trx->save();
      // 	} elseif ($product->id == 88 && $user->flag == 1) {
      // 		$result = $joyhelper->downgradeToSC($user->uid);
      // 	} elseif ($product->id == 2 && $user->flag != 2) {
      // 		$user->flag = 2;
      // 		$user->activated_at = $trx->transaction_date;
      // 		$user->upid = null;
      // 		$user->status = 1;
      // 		$user->save();

      // 		$parent = User::where('uid', $user->owner)->first();
      // 		$parent->flag = 2;
      // 		$parent->activated_at = $trx->transaction_date;
      // 		$parent->id_upline_fk = null;
      // 		$parent->status = 1;
      // 		$parent->save();
      // 	}
      // 	#endif

      // 	#before if product qudu maka point qudu bertambah 1
      // 	#after if product xpress jps maka point qudu bertambah 1
      // 	if ($product->id_jenis_fk == 6 || $product->id_jenis_fk == 8) {
      // 		$qudu += $trxd->qty;
      // 		$quduBVG += $trxd->qty * $product->pv;
      // 	}

      // 	if ($product->is_register == 1) {

      // 		$parent = User::where('uid', $trx->membership->owner)->first();
      // 		$parent->flag = 1;
      // 		$parent->activated_at = $parent->status == 1 ? $parent->activated_at : $trx->transaction_date;
      // 		$parent->status = 1;
      // 		$parent->save();

      // 		$membership->flag = $product->is_register;
      // 		$membership->activated_at = $trx->transaction_date;
      // 		$membership->status = 1;

      // 		$membership->save();

      // 		$message_user = "Selamat! membership anda telah aktif. Login dengan " . $user->email . " & password yg didaftarkan. URL referral anda: www.joybiz.co.id/" . $user->username;

      // 		$message_sponsor = "Selamat! " . $user->username . " yang anda Sponsori telah aktif. Lakukan pengaturan upline di menu placement. Atau berlaku placement otomatis setiap hari jam 23.59 WIB";


      // 		$destination_user = $user->handphone;
      // 		$result_user = $sms->send($destination_user, $message_user);

      // 		$destination_sponsor = $membership->sponsor->user->handphone;
      // 		$result_sponsor = $sms->send($destination_sponsor, $message_sponsor);

      // 		$hasRegister = true;
      // 	} elseif ($product->pv > 0) {
      // 		$totalHargaWIB += $trxd->qty * $product->harga_1;
      // 		if ($user->flag == 3) {
      // 			$zone = $trx->province->kelompok ?? 3;
      // 			$selisihRetail = $product['harga_retail_' . $zone] - $product['harga_' . $zone];
      // 		}
      // 	}
      // }



      #jika transaksi sc terdapat paket register joybizer makan transaksi tersebut di rubah menjadi transaksi joybizer
      if ($trx->id_sc_fk && $hasRegister) {
        $trx->id_cust_fk = $trx->id_sc_fk;
        $trx->id_sc_fk = null;
      }

      $trx->transaction_date = $date;
      if ($trx->status == 'COD') {
        $trx->status = 'S';
      } else {
        $trx->status = $indent ? 'I' : 'PC';
      }

      $trx->approved_date = Carbon::now();
      $trx->approved_by = $userlogin;
      $trx->save();

      if ($trx->total_pv >= 100 && $user->status == 3) { // status = 3 is dormant
        $member = Member::where('uuid', $trx->member_uuid);
        $member->will_dormant_at = Carbon::parse($trx->transaction_date)->addMonths(6)->toDateString();
        $member->save();
      }


      if (
        $membership->status != 1
        && $membership->membership_status == 1
        && $trx->total_bv >= $membership->min_bv
      ) {
        $membership->activated_at = $membership->status == 1 && $trx->pv_total >= $membership->min_bv
          ? $membership->activated_at
          : $trx->transaction_date;
        $membership->status = 1; // Active
        $membership->membership_status = 1; // Member
        $membership->save();
      }

      //jika transaksi Special Customer
      if ($trx->member_uuid) {
        $sCTrx = $this->calculateSCAmount($trx->uuid);

        if ($trx->total_pv > 0) {

          if ($membership->membership_status == 2 || $hasRegister) {

            //if Customer buy with retail once change to SC
            $user->membership_status = 2;
            $user->status = 1;
            $user->activated_at = $date;
            $user->save();

            // #jika customer tidak upgrade membership
            // if ($user->membership_status == 2) {
            // 	$message_user = "Selamat! anda telah menjadi Special Customer kami. Login dengan " . $user->email . " & password yg didaftarkan di www.joybiz.co.id/";
            // 	$message_sponsor = "Selamat! Special Customer " . $user->username . " yang anda Sponsori telah aktif.";

            // 	$destination_user = $user->handphone;
            // 	$result_user = $sms->send($destination_user, $message_user);

            // 	// $destination_sponsor = $user->sponsor->handphone;
            // 	$destination_sponsor = $membership->sponsor->user->handphone;
            // 	$result_sponsor = $sms->send($destination_sponsor, $message_sponsor);
            // }
          }

          //$RealBV = ($trx->bv_total * 0.875);				
          //$Cashback = $totalHargaWIB * 0.083;

          $realBV = $sCTrx['SBV'];
          $realPV = $sCTrx['SPV'];
          $cashback = $sCTrx['Cashback'];

          //$Cashback = $SH1 * 0.083;

          //if ($Cashback && !$voucher_amount){
          if ($cashback) {
            $owner = Member::where('uuid', $trx->member_uuid)->first();
            $note = "Cashback from your transaction " . $trx->uuid;
            $TransferCashback = $money->topupVoucher($owner->uuid, $cashback, $note, $userlogin);
          }

          $selisihRetail -= 20000;
          $selisihRetail = $selisihRetail > 0 ? $selisihRetail : 0;
          if ($selisihRetail) {
            $owner = Member::where('uuid', $trx->member_uuid)->first();
            $note = "Cashback from Special Customer Registration " . $trx->code_trans;
            $TransferCashback = $money->topupVoucher($owner->uuid, $selisihRetail, $note, $userlogin);
          }
        }

        /* joybiz v1 
				elseif($product->pv > 0 && $product->is_register){
					//jika SC1 & SC2 
					if(in_array($product->kode, $scRegisterSpecialCase)){
						$totalHargaWIB += $trxd->qty * $product->harga_1 - 37500;
						$selisihRegisterSC = 17500;
					}elseif(in_array($product->kode, $scRegisterSpecialCase)){
						$totalHargaWIB += $trxd->qty * $product->harga_1 - 10000;
						$selisihRegisterSC = 80000;
					}else{
						$totalHargaWIB += $trxd->qty * $product->harga_1;
					}
				}
				*/
      }

      // //jika cod kasi voucher ke sponsor
      // if ($trx->cod) {
      // 	$owner = Member::where('uuid', $trx->member_uuid)->first();
      // 	$fee = ($trx->purchase_cost + $trx->shipping_cost) * 0.03;
      // 	$codCashback = ($trx->purchase_cost - $hargaAsliCod) - $fee;

      // 	$year = Carbon::now()->year;
      // 	$totalBonus = \App\BonusWeekly::where([['owner', $owner->uid], ['year', $year]])->get()->sum('total');
      // 	$totalBonus += \App\BonusMonthlySummary::where([['owner', $owner->uid], ['year', $year]])->get()->sum('total');
      // 	$totalBonus += \App\PointCenturionWinner::where([['owner', $owner->uid]])->get()->sum('amount');
      // 	$totalBonus += \App\JoyBonusSummary::where([['owner', $owner->uid], ['confirmed', true]])->whereYear('date', $year)->get()->sum('total');

      // 	$tax = new TaxID;
      // 	$pph = round($tax->getTaxAmount($totalBonus, $codCashback));
      // 	if (!$owner->no_npwp) $pph += $pph * 0.2;
      // 	$codCashback -= $pph;

      // 	$note = "Cashback dari transaksi COD " . $trx->code_trans . " sebesar " . number_format($codCashback);
      // 	$TransferCashbackCod = $money->topupVoucher($owner->uid, $codCashback, $note, $userlogin);

      // 	$codProfit = CodProfit::create(['owner' => $owner->uid, 'code' => $trx->code_trans, 'member_price' => $hargaAsliCod, 'retail_price' => $trx->purchase_cost, 'cod_fee' => $fee, 'pph' => $pph, 'voucher' => $codCashback, 'vouchered' => $TransferCashbackCod]);
      // 	//$trx->cod_voucher = $TransferCashbackCod;
      // 	$trx->save();
      // }

      //abodemen
      $transaction = new OrderHeader;
      // $result = 
      $transaction->generateAbodemenChild($trx->code_trans);

      // #$userCoupon = User::where('id',$trx->id_cust_fk)->first();
      // #$RewardCoupon = $this->monthyRewardCoupon($userCoupon);

      // if ($trx->is_pickup) {
      // 	$trx->pickup_code = encrypt(rand(111111, 999999));
      // 	$trx->save();
      // }


      // if ($saved) {

      // #cashback
      // foreach ($trx['transaksi_detail'] as $trxd) {

      // 	// if($trxd->id_barang_fk == 720){
      // 	// 	$product = barang::where('id',$trxd->id_barang_fk)->first();
      // 	// 	$cashback_amount = 90000 * $trxd->qty;
      // 	// 	$description = "Cashback ".$product->nama." dari Transaksi ".$trx->code_trans;

      // 	// 	$money->money("cashback",$membership, $cashback_amount, false, $description,false,$trx->code_trans);
      // 	// } elseif($trxd->id_barang_fk == 721){
      // 	// 	$product = barang::where('id',$trxd->id_barang_fk)->first();
      // 	// 	$cashback_amount = 45000 * $trxd->qty;
      // 	// 	$description = "Cashback ".$product->nama." dari Transaksi ".$trx->code_trans;

      // 	// 	$money->money("cashback",$membership, $cashback_amount, false, $description,false,$trx->code_trans);
      // 	// }

      // 	$product = barang::where('id', $trxd->id_barang_fk)->first();
      // 	if ($product->cashback_gamma > 0 && $srank && $srank->srank >= 3) {
      // 		$cashback_amount = $product->cashback_gamma * $trxd->qty;
      // 		$description = "Cashback " . $product->nama . " dari Transaksi " . $trx->code_trans;

      // 		//money($type,$user, $amount, $credit, $description,$freeze=false,$transaction_code = null)
      // 		$money->money("cashback", $membership, $cashback_amount, false, $description, false, $trx->code_trans);
      // 	}
      // }
      #cashback

      $jbid = $trx->member_uuid;

      $ppv = $trx->total_pv;
      $pbv = $trx->total_bv;
      $prv = $trx->total_rv;

      $gpv = 0;
      $gbv = 0;
      $grv = 0;

      $jbp = 0;
      $bj = 0;
      $vj = 0;

      $ppvj = $trx->total_pv_plan_biz;
      $pbvj = $trx->total_bv_plan_biz;
      $prvj = $trx->total_rv_plan_biz;
      $gpvj = 0;
      $gbvj = 0;
      $grvj = 0;

      $ppvb = 0;
      $pbvb = 0;
      $gpvb = 0;
      $gbvb = 0;

      $omzet = $trx->purchase_cost;
      $omzet_joy = $trx->price_joy;
      $omzet_biz = $trx->price_biz;
      $omzet_with_bv = $trx->omzet_with_bv
        ? ($trx->omzet_with_bv / 20000)
        : ($omzet_joy + $omzet_biz) / 20000;

      if ($pbv) {
        $this->update_Effective_Rank_Sim(
          $jbid,
          $ppv,
          $pbv,
          $gpv,
          $gbv,
          $date,
          $jbp,
          $bj,
          $vj,
          $ppvj,
          $pbvj,
          $gpvj,
          $gbvj,
          $ppvb,
          $pbvb,
          $gpvb,
          $gbvb,
          $omzet,
          $omzet_joy,
          $omzet_biz,
          $omzet_with_bv,
          $prvj,
          $grvj,
          $prv,
          $grv
        );
      }

      // $result = 
      $joyhelper->syncJoyDatabyCode($uuid);

      $status = true;
      $message = "Transaction " . $trx->uuid . " settled with success!!";
    } else {

      $status = false;
      $message = "Transaction not found!!";
    }

    return ['status' => $status, 'message' => $message];
  }

  public function calculateSCAmount($uuid)
  {
    $trx = OrderHeader::where('uuid', $uuid)->with(['details', 'shipping', 'user'])->first();
    $point_reward = $trx->total_voucher_amount
      ? $trx->total_voucher_amount
      : 0;

    $sH1 = 0;
    $sPV = 0;
    $sBV = 0;
    $cashback = 0;

    foreach ($trx->details as $trxd) {
      // $product = barang::where('id', $trxd->id_barang_fk)->first();

      // if ($product->bv) {
      $percentPV = $trxd->pv / $trxd->price;
      $percentBV = $trxd->bv / $trxd->price;
      $tH1 = $trxd->qty * $trxd->price;

      $sisaTH1 = $point_reward >= $tH1 ? 0 : $tH1 - $point_reward;
      $point_reward -= $tH1;

      if ($sisaTH1) {
        $sH1 += $sisaTH1;
        $sPV += $sisaTH1 * $percentPV;

        $bv = $sisaTH1 * $percentBV;
        $bv -= $bv * 0.05;
        $sBV += $bv;

        $cashback += $sisaTH1 * ($trxd->cashback / 100);
      }
      // }
    }

    return [
      'SH1' => $sH1,
      'SPV' => $sPV,
      'SBV' => $sBV,
      'Cashback' => $cashback
    ];
  }

  public function monthyRewardCoupon($member)
  {
    $curr = $this->getCurrentMonth();

    $mid = $this->getMid($curr['end_date']);
    $trxs = OrderHeader::where('member_uuid', $member->uuid)
      // ->whereNotIn('status', ['WP', 'P'])
      ->whereBetween('transaction_date', [$curr['start_date'], $curr['end_date']])
      ->get();

    $srank = $member->load('srank');
    $srank = $srank->srank ? $srank->srank->srank : 0;

    $tpv = $trxs->sum('total_pv');

    $qtyCoupon = 0;
    if ($srank >= 5) {
      $qtyCoupon = floor($tpv / 120);
    } elseif ($srank >= 3) {
      $qtyCoupon = floor($tpv / 160);
    } else {
      $qtyCoupon = floor($tpv / 400);
    }

    $currCoupon = MonthlyRewardCoupon::where([
      ['member_uuid', $member->uuid],
      ['mid', $mid]
    ])->get();
    if ($qtyCoupon > $currCoupon->count()) {
      for ($i = 0; $i < $qtyCoupon - $currCoupon->count(); $i++) {
        $now = Carbon::now();
        $vcode = Str::random(2) . $now->second . Str::random(2) . $now->minute;
        $result = MonthlyRewardCoupon::create([
          'uuid' => Str::uuid(),
          'member_uuid' => $member->uuid,
          'voucher' => $vcode,
          'mid' => $mid
        ]);
      }
    }
  }

  //update Joy & Biz Plan requirement data
  public function resyncPlanJoyTransisi(
    $member_uuid,
    $ppv,
    $pbv,
    $gpv,
    $gbv,
    $updated_at,
    $jbp,
    $bj,
    $vj,
    $ppvj,
    $pbvj,
    $gpvj,
    $gbvj,
    $ppvb,
    $pbvb,
    $gpvb,
    $gbvb,
    $omzet,
    $ozj,
    $ozb,
    $opc,
  ) {
    $user = Member::select('id', 'sponsor_uuid', 'placement_uuid', 'uuid')
      ->where('uuid', $member_uuid)
      ->first();

    if ($user) {
      $spid = $user->sponsor_uuid;
      $upid = $user->placement_uuid;

      $wid = Period::where([
        ['start_date', '<=', $updated_at],
        ['end_date', '>=', $updated_at]
      ])->first(['id', 'start_date', 'end_date']);
      $mid = date('Ym', strtotime($wid->sDate));

      //sementara
      $mid2 = date('Ym', strtotime($wid->eDate));

      /* update status rank */
      $srank = SRank::updateOrCreate(
        ['member_uuid' => $member_uuid],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );
      $leg_jbp = SRank::where([
        ['sponsor_uuid', $member_uuid],
        ['jbp', '>', 0]
      ])->orWhere([
        ['placement_uuid', $member_uuid],
        ['srank', 5]
      ])->count();
      $current_rank = is_null($srank->srank) ? 0 : $srank->srank;

      $srank_downlines = SRank::where('placement_uuid', $member_uuid)->get();

      $tot_bj = 0;
      $tot_vj = 0;

      $tot_vj_temp = 0;
      $tot_bj_temp = 0;


      foreach ($srank_downlines as $srank_downline) {
        if ($srank_downline->srank == 6) {
          $tot_bj_temp = 1 + $srank_downline->bj;
        } else {
          $tot_bj_temp = $srank_downline->bj;
        }

        if ($srank_downline->srank >= 7) {
          $tot_vj_temp = 1 + $srank_downline->vj;
        } else {
          $tot_vj_temp = $srank_downline->vj;
        }


        $tot_vj += ($tot_vj_temp > 3) ? 3 : $tot_vj_temp;
        $tot_bj += ($tot_bj_temp > 3) ? 3 : $tot_bj_temp;
      }

      $newRank = $this->check_Rank(
        $srank->appv,
        $leg_jbp,
        $tot_bj,
        $tot_vj,
        $user
      );

      $srank->jbp += $jbp;
      $srank->bj += $bj;
      $srank->vj += $vj;
      $srank->srank = $newRank > $srank->srank ? $newRank : $srank->srank;
      $srank->save();


      if (($newRank >= 5) && ($newRank > $current_rank)) {
        switch ($current_rank) {
          case 5:
            $jbp -= 1;
            break;
          case 6:
            $bj -= 1;
            break;
        }

        switch ($newRank) {
          case 5:
            $jbp += 1;
            break;
          case 6:
            $bj += 1;
            break;
          case 7:
            $vj += 1;
            break;
        }
      }

      /* update status rank done */

      $erank = ERank::updateOrCreate(
        ['member_uuid' => $member_uuid, 'mid' => $mid],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );


      //sementara
      $erank2 = ERank::updateOrCreate(
        ['member_uuid' => $member_uuid, 'mid' => $mid2],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );

      if ($erank->erank < $erank2->erank) {
        $current_erank = is_null($erank2->erank) ? 0 : $erank2->erank;
        $erank = $erank2;
      } else {
        $current_erank = is_null($erank->erank) ? 0 : $erank->erank;
      }


      $erank->gpv += $gbv;
      $erank->erank = $this->check_eRank($member_uuid, $erank->ppv, $mid);
      $erank->save();

      $preparedDataJoy = PreparedDataJoy::firstOrCreate(
        ['member_uuid' => $member_uuid, 'wid' => $wid->id],
        ['sponsor_uuid' => $spid, 'placement_uuid' => $upid]
      );

      $preparedDataJoy->ppv += $ppv;
      $preparedDataJoy->pbv += $pbv;
      $preparedDataJoy->ppvj += $ppvj;
      $preparedDataJoy->pbvj += $pbvj;
      $preparedDataJoy->gpvj += $gpvj;
      $preparedDataJoy->gbvj += $gbvj;
      $preparedDataJoy->omzet += $omzet;
      $preparedDataJoy->ozj += $ozj;
      $preparedDataJoy->srank = $srank->srank;
      $preparedDataJoy->erank = $erank->erank;
      $preparedDataJoy->opc += $opc;
      $preparedDataJoy->save();


      /* :D */
      $ppv  += $gpv;
      $pbv  += $gbv;
      $ppvj += $gpvj;
      $pbvj += $gbvj;
      $ppvb += $gpvb;
      $pbvb += $gbvb;
      /* :D */

      $this->resyncPlanJoyTransisi(
        $upid,
        0,
        0,
        $ppv,
        $pbv,
        $updated_at,
        $jbp,
        $bj,
        $vj,
        0,
        0,
        $ppvj,
        $pbvj,
        0,
        0,
        $ppvb,
        $pbvb,
        $omzet,
        $ozj,
        $ozb,
        $opc,
      );
    }
  }

  public function getWID($date)
  {
    return Period::where([
      ['start_date', '<=', $date],
      ['end_date', '>=', $date]
    ])->first(['id', 'start_date', 'end_date']);
  }

  public function getMid($date)
  {
    $wid = Period::where([
      ['start_date', '<=', $date],
      ['end_date', '>=', $date]
    ])->first(['id', 'start_date', 'end_date']);

    return date('Ym', strtotime($wid->eDate));
  }

  public function getMonthName($monthNumber)
  {
    return date("F", mktime(0, 0, 0, $monthNumber, 1));
  }

  public function getCurrentMonth()
  {
    $now = Carbon::now();

    $periode = Period::where([
      ['start_date', '<=', $now->toDateString()],
      ['end_date', '>=', $now->toDateString()]
    ])->first();

    $now = Carbon::parse($periode->eDate);
    $month = $now->month;
    $year =  $now->year;

    $weeks = Period::whereMonth('end_date', $month)
      ->whereYear('end_date', $year)
      ->orderBy('id', 'asc')
      ->get();

    $sDate = $weeks->first()->sDate;
    $eDate = $weeks->last()->eDate;

    return array('start_date' => $sDate, 'end_date' => $eDate);
  }

  public function getPeriodeMonth($month, $year)
  {
    $weeks = Period::whereMonth('end_date', $month)->whereYear('end_date', $year)->orderBy('id', 'asc')->get();
    $start = $weeks->first();
    $end = $weeks->last();


    return array('start_date' => $start->sDate, 'end_date' => $end->eDate, 'swid' => $start->id, 'ewid' => $end->id);
  }

  public function getPeriodeMonthBetween($m)
  {
    $now = Carbon::now();

    $week = Period::where([['start_date', '<=', $now], ['end_date', '>=', $now]])->first();

    $month = date('m', strtotime($week->eDate));
    $year = date('Y', strtotime($week->eDate));

    $from = $now->subMonths($m);

    $weeks = Period::whereMonth('end_date', $from->month)
      ->whereYear('end_date', $from->year)
      ->orderBy('id', 'asc')->get();
    $sDate = $weeks->first()->sDate;

    $weeks = Period::whereMonth('end_date', $month)
      ->whereYear('end_date', $year)
      ->orderBy('id', 'desc')
      ->get();
    $eDate = $weeks->first()->eDate;

    return array('start_date' => $sDate, 'end_date' => $eDate);
  }

  public function getOnGoingMonth()
  {
    $now = Carbon::now();
    $wid = Period::where([
      ['start_date', '<=', $now],
      ['end_date', '>=', $now]
    ])->first();

    $month = date('m', strtotime($wid->eDate));
    $year =  date('Y', strtotime($wid->eDate));

    $weeks = Period::whereMonth('end_date', $month)
      ->whereYear('end_date', $year)
      ->orderBy('id', 'asc')
      ->get();

    $sDate = $weeks->first()->end_date;
    $eDate = $weeks->last()->end_date;

    return array('start_date' => $sDate, 'end_date' => $eDate);
  }

  public function void($uuid)
  {
    $emoney = new Money;
    $trx = OrderHeader::where('uuid', $uuid)
      ->with(['user', 'specialCustomer', 'details'])
      ->first();

    if ($trx) {
      $register = OrderDetail::where('order_header_uuid', $trx->uuid)
        // ->whereIn('id_barang_fk', [1, 2, 16, 17, 18])
        ->whereHas('productPrice.product', function ($query) {
          $query->whereIn('uuid', [1, 2, 16, 17, 18]);
        })

        ->first();
      try {
        DB::beginTransaction();


        // "P"=>"Process",
        // "WP"=>"Pending",
        // "CP"=>"Menunggu Verifikasi",
        // "PC"=>"Settlement",
        // "PR"=>"Pembayaran ditolak",
        // "S"=>"Delivered",
        // "A"=>"Picked",
        // "R"=> "Transaksi ditolak",
        // "I"=> "Indent",        
        // "J"=> "Partial",
        // "X"=> "Promo Anniversary",
        // "COD"=> "Cash On Delivery",

        // 'Status : 0 = Pending, 1 = Paid, 2 = Posted, 3 = Rejected, 4 = Waiting For Prepared, 
        // 5 = Prepared From Warehouse, 6 = Picked Up By Courier, 7 = Delivered'


        // if (in_array($trx->status, ['PC', 'A', 'S', 'I'])) {
        if ($trx) {
          $mid = $this->getMid($trx->transaction_date);

          $srank = SRank::where('member_uuid', $trx->member_uuid)->first();
          if ($srank) {
            $srank->appv -= $trx->total_pv;
            $srank->apbv -= $trx->total_bv;
            $srank->save();
          }

          $erank = ERank::where([
            ['member_uuid', $trx->member_uuid], ['mid', $mid]
          ])->first();
          if ($erank) {
            $erank->ppv -= $trx->total_bv;
            $erank->save();
          }

          // $do = DeliveryOrder::where('code_trans', $trx->code_trans)->first();
          // if ($do) {
          // 	$stock_card = stock_card::where('kode', $do->code)->delete();
          // 	$do->delete();
          // }
        }

        //refund voucher
        if ($trx->total_voucher_amount > 0) {
          $note = "Refund from void transaction " . $trx->code_trans;

          if ($trx->member_uuid) {
            $refund = $emoney->topupVoucher($trx->member_uuid, $trx->total_voucher_amount, $note, null);
          } else {
            $refund = $emoney->topupVoucher($trx->member_uuid, $trx->total_voucher_amount, $note, null);
          }
        }

        // if ($trx->airway_bill_no && $trx->status == 6) {//&& $trx->pickup_stock_pack 
        //   foreach ($trx->details as $key => $detail) {
        //     foreach ($detail->barang->barang_detail as $bd) {

        //       $stock_detail = StockPackStockDetail::updateOrCreate([
        //         'jspid' => $trx->pickup_stock_pack,
        //         'tcode' => $trx->code_trans, 'pcode' => $bd->barang->kode, 'in' => true
        //       ], ['qty' => $bd->qty]);
        //       $stock = StockPackStock::firstOrCreate(['jspid' => $trx->pickup_stock_pack, 'pcode' => $bd->barang->kode]);
        //       $stock->qty += $bd->qty;
        //       $stock->save();
        //     }
        //   }
        // }


        $member = Member::where('uuid', $trx->member_uuid)->first();
        if ($register) {
          // $member = Member::where('jbid',$trx->id_cust_fk)->first();
          if ($member->activated_at == null) {
            // $member->status = null;
            // $member->no_ktp = null;					
            // $member->save();
          }
        }

        #cashback
        $cashbacks = VoucherCashbackDetail::where([
          ['transaction_uuid', $trx->uuid],
          ['uid', $member->uuid]
        ])
          ->get();

        foreach ($cashbacks as $cashback) {
          $description = "Void Transaksi " . $trx->uuid;

          $emoney->money(
            "cashback",
            $member,
            decrypt($cashback->encrypted_amount),
            false,
            $description,
            false,
            $trx->uuid
          );
        }
        #cashback

        // $so = SpecialOffer::where('code_trans', $trx->code_trans)->delete();
        $trx_detail = orderDetail::where('order_header_uuid', $trx->uuid)->delete();
        $result = $trx->delete();

        DB::commit();
      } catch (\PDOException $e) {
        DB::rollBack();
        echo $e;
      }
    } else {
      $result = "Transasction not found";
    }

    if (!isset($result)) {
      $result = "Void transaksi dengan kode " . $uuid . " gagal";
    }

    return $result;
  }

  public function void7hari($uuid)
  {
    $emoney = new Money;
    $trx = OrderHeader::where('uuid', $uuid)
      ->with('user', 'special_customer')
      ->first();
    $register = OrderDetail::where('order_header_uuid', $trx->uuid)
      ->whereHas('productPrice.product', function ($query) {
        $query->whereIn('uuid', [1, 2, 16, 17, 18]);
      })
      ->first();

    if ($trx) {
      try {
        DB::beginTransaction();

        $mid = $this->getMid($trx->transaction_date);

        $srank = SRank::where('member_uuid', $trx->member_uuid)->first();
        if ($srank) {
          $srank->appv -= $trx->total_pv;
          $srank->save();
        }

        $erank = ERank::where([['member_uuid', $trx->member_uuid], ['mid', $mid]])->first();
        if ($erank) {
          $erank->ppv -= $trx->total_bv;
          $erank->save();
        }

        // $do = DeliveryOrder::where('code_trans', $trx->uuid)->first();
        // if ($do) {
        //   $stock_card = stock_card::where('kode', $do->code)->delete();
        //   $do->delete();
        // }

        //refund voucher
        if ($trx->total_voucher_amount > 0) {
          $note = "Refund from void transaction " . $trx->uuid;
          if ($trx->member_uuid) {
            $refund = $emoney->topupVoucher($trx->uid, $trx->voucher_amount, $note, null);
          } else {
            $refund = $emoney->topupVoucher($trx['user']->uid, $trx->voucher_amount, $note, null);
          }
        }



        $member = Member::with('user')
          ->where('uuid', $trx->member_uuid)
          ->first();



        if ($register && $member->activated_at == null) { // Pending
          $member->status = 5;
          $member->id_no = null;
          $member->save();
        }

        #cashback
        $cashbacks = VoucherCashbackDetail::where([
          ['transaction_code', $trx->uuid],
          ['uid', $member->uid]
        ])->get();
        foreach ($cashbacks as $cashback) {
          $description = "Void Transaksi " . $trx->uuid;
          $emoney->money(
            "cashback",
            $member,
            decrypt($cashback->encrypted_amount),
            false,
            $description,
            false,
            $trx->uuid
          );
        }
        #cashback

        // $SO = SpecialOffer::where('code_trans', $trx->code_trans)->delete();
        $trx_detail = OrderDetail::where('order_header_uuid', $trx->uuid)->delete();

        $result = $trx->delete();

        DB::commit();
      } catch (\PDOException $e) {
        DB::rollBack();
        echo $e;
      }
    } else {
      $result = "Transasction not found";
    }

    return $result;
  }

  public function voidPaymentGateway($uuid)
  {
    $emoney = new Money;
    $trx = OrderHeader::where('uuid', $uuid)
      // ->whereNotIn('status', ['PC', 'S', 'A'])
      ->with('member')
      ->first();

    if ($trx) {
      $register = OrderDetail::where('order_header_uuid', $trx->uuid)
        // ->whereIn('id_barang_fk', [1, 2, 16, 17, 18])
        ->first();
      try {
        DB::beginTransaction();


        $mid = $this->getMid($trx->transaction_date);

        $srank = SRank::where('uuid', $trx->member_uuid)->first();
        if ($srank) {
          $srank->appv -= $trx->pv_total;
          $srank->save();
        }

        $erank = ERank::where([['uuid', $trx->member_uuid], ['mid', $mid]])->first();
        if ($erank) {
          $erank->ppv -= $trx->bv_total;
          $erank->save();
        }

        // $do = DeliveryOrder::where('code_trans', $trx->code_trans)->first();
        // if ($do) {
        //   $stock_card = stock_card::where('kode', $do->code)->delete();
        //   $do->delete();
        // }

        //refund voucher
        if ($trx->total_voucher_amount > 0) {
          $note = "Refund from void transaction " . $trx->code_trans;
          $refund = $emoney->topupVoucher($trx['user']->uid, $trx->total_voucher_amount, $note, null);
        }

        /*
				if ($register){
					$user = User::where('id',$trx->id_cust_fk)->first();
					$user->status = null;
					$user->activated_at = null;
					$user->save();					
				}*/

        $member = Member::where('uuid', $trx->member_uuid)->first();
        #cashback
        $cashbacks = VoucherCashbackDetail::where([
          ['transaction_uuid', $trx->uuid],
          ['uid', $member->uid]
        ])->get();
        foreach ($cashbacks as $cashback) {
          $description = "Void Transaksi " . $trx->code_trans;
          $emoney->money("cashback", $member, decrypt($cashback->encrypted_amount), false, $description, false, $trx->code_trans);
        }
        #cashback

        // $sO = SpecialOffer::where('code_trans', $trx->code_trans)->delete();
        $trx_detail = OrderDetail::where('order_header_uuid', $trx->uuid)->delete();
        $result = $trx->delete();

        DB::commit();
      } catch (\PDOException $e) {
        DB::rollBack();
        echo $e;
      }
    } else {
      $result = "Transasction not found";
    }

    return $result;
  }

  public function getSponsor($uuid)
  {
    $user = Member::where([
      ['member_uuid', $uuid],
      ['activated_at', '!=', null],
      ['flag', 1]
    ])->first();
    $sponsor = Member::where([
      ['uuid', $user->sponsor_uuid],
      ['activated_at', '!=', null],
      ['flag', 1]
    ])->first();

    return $sponsor;
  }

  public function isCarryWeek($wid)
  {

    //if next week is new month
    $current = Period::where('id', $wid)->first();
    $next = Period::where('id', $wid + 1)->first();
    $sMonth = date("m", strtotime($current->eDate));
    $eMonth = date("m", strtotime($next->eDate));

    if ($sMonth == $eMonth) {
      return true;
    } else {
      return false;
    }
  }

  public function getCurrentWeek()
  {
    $now = Carbon::now();
    $month = $now->month;
    $year =  $now->year;

    $weeks = Period::whereMonth('end_date', $month)->whereYear('end_date', $year)->orderBy('id', 'asc')->get();

    $sDate = $weeks->first()->sDate;
    $eDate = $weeks->last()->eDate;

    return array('start_date' => $sDate, 'end_date' => $eDate);
  }

  public function useVoucher($member_uuid, $amount, $uuid)
  {
    $voucher = Voucher::where('member_uuid', $member_uuid)->first();
    $saldo = decrypt($voucher->saldo);

    $now = Carbon::now();
    $sDate = Carbon::now()->startOfMonth();
    $eDate = $now->endOfMonth();
    $index = VoucherDetail::whereBetween('created_at', [$sDate, $eDate])->count() + 1;

    $code = "UVCI" . $now->year . $now->format('m') . $index . rand(111, 999);
    $note = "Voucher dipakai oleh " . $member_uuid . " pada " . $now . " untuk transaksi " . $uuid . " issued_by " . $member_uuid;

    try {
      DB::beginTransaction();
      $result = VoucherDetail::create([
        'member_uuid' => $member_uuid,
        'code' => $code,
        'debit' => encrypt(0),
        'credit' => encrypt($amount),
        'note' => $note
      ]);

      $new_saldo = $saldo - $amount;
      $voucher->saldo = encrypt($new_saldo);
      $voucher->save();
      DB::commit();
    } catch (\PDOException $e) {
      DB::rollBack();
      Log::error($e);
    }

    return $code;
  }

  public static function getSaldoVoucher($member_uuid)
  {
    $voucher = Voucher::where('member_uuid', $member_uuid)->first();
    if ($voucher) return $voucher->saldo;
  }

  public static function getSaldoVoucherCashback($member_uuid)
  {
    $voucher = VoucherCashback::where('member_uuid', $member_uuid)->first();
    if ($voucher) return decrypt($voucher->encrypted_amount);
  }

  public function pickSponsorByProvince($ppv, $province)
  {

    $prodate = $this->getPeriodeMonth(Carbon::now()->month, Carbon::now()->year);

    if ($province) {
      $raws = OrderHeader::leftjoin('users', function ($join) use ($province) {
        $join->on('transaction.id_cust_fk', '=', 'users.id');
      })->whereRaw("users.provinsi = '" . $province . "' AND transaction.transaction_date BETWEEN '" . $prodate['start_date'] . "' AND '" . $prodate['end_date'] . "'")
        ->groupBy('id_cust_fk', 'username')
        ->selectRaw('sum(pv_total) as tpv, username, id_cust_fk')
        ->get()->pluck('id_cust_fk');
    } else {
      $raws = OrderHeader::leftjoin('users', function ($join) use ($province) {
        $join->on('transaction.id_cust_fk', '=', 'users.id');
      })->whereRaw("transaction.transaction_date BETWEEN '" . $prodate['start_date'] . "' AND '" . $prodate['end_date'] . "'")
        ->groupBy('id_cust_fk', 'username')
        ->selectRaw('sum(pv_total) as tpv, username, id_cust_fk')
        ->get()->pluck('id_cust_fk');
    }

    if ($province && !count($raws)) {
      $spid = $this->pickSponsorByProvince($ppv, null);
    } else {
      $spid = $raws->count() ? $raws[rand(0, count($raws))] : 3;
    }

    return $spid;
  }

  public function flatten($array)
  {
    $result = [];
    foreach ($array as $item) {
      if (is_array($item)) {
        $result[] = array_filter($item, function ($array) {
          return !is_array($array);
        });
        $result = array_merge($result, $this->flatten($item));
      }
    }
    return array_filter($result);
  }

  #var $rank_name = ['-','C','JK','JPS','JP','JBP','BJ','VJ','EJ','MJ','DJ','CA','RCA'];
  var $rank_name = ['-', 'α', 'β', 'Ɣ', 'JPS', 'JP', 'JBP', 'BJ', 'VJ', 'EJ', 'MJ', 'DJ', 'CA', 'RCA'];

  public function prepareTreeData($root, $wid, $mid)
  {

    if (count($root['children'])) {
      foreach ($root['children'] as $key => $child) {
        $tree[0]['children'][$key] = $this->getchild($child, $wid, $mid);
      }
    }

    $pdj = $root->load(['npdj' => function ($q) use ($wid, $mid) {
      return $q->where('wid', $wid)->with(['effectiveRank' => function ($query) use ($mid) {
        return $query->where('mid', $mid);
      }]);
    }]);


    $tree[0]['username'] = $root['username'];
    $tree[0]['nama'] = $root->nama;

    $tree[0]['ppv'] = !empty($pdj->npdj) ? $pdj->npdj->ppv : 0;
    $tree[0]['pbv'] = !empty($pdj->npdj) ? $pdj->npdj->pbv : 0;
    $tree[0]['gpvj'] = !empty($pdj->npdj) ? $pdj->npdj->gpvj : 0;
    $tree[0]['gbvj'] = !empty($pdj->npdj) ? $pdj->npdj->gbvj : 0;

    $tree[0]['pro'] = !empty($pdj->npdj->effectiveRank) ? $pdj->npdj->effectiveRank[0]->ppv : 0;
    $tree[0]['erank'] = $this->rank_name[!empty($pdj->npdj->effectiveRank) ? $pdj->npdj->effectiveRank[0]->erank : 0];



    return $tree;
  }

  public function getchild($children, $wid, $mid)
  {

    if (count($children['children'])) {
      foreach ($children['children'] as $key => $child2) {
        $child['children'][$key] = $this->getchild($child2, $wid, $mid);
      }
    } else {
      $child['hasChildren'] = true;
    }

    $pdj = $children->load(['npdj' => function ($q) use ($wid, $mid) {
      return $q->where('wid', $wid)->with(['effectiveRank' => function ($query) use ($mid) {
        return $query->where('mid', $mid);
      }]);
    }]);

    $child['username'] = $children['username'];
    $child['nama'] = $children->nama;

    $child['ppv'] = !empty($pdj->npdj) ? $pdj->npdj->ppv : 0;
    $child['pbv'] = !empty($pdj->npdj) ? $pdj->npdj->pbv : 0;
    $child['gpvj'] = !empty($pdj->npdj) ? $pdj->npdj->gpvj : 0;
    $child['gbvj'] = !empty($pdj->npdj) ? $pdj->npdj->gbvj : 0;

    $child['pro'] = !empty($pdj->npdj->effectiveRank) ? $pdj->npdj->effectiveRank[0]->ppv : 0;
    $child['erank'] = $this->rank_name[!empty($pdj->npdj->effectiveRank) ? $pdj->npdj->effectiveRank[0]->erank : 0];

    //$child['appv'] = isset($children['srank']) ? $children['srank']['appv'] : 0;
    //$child['srank'] = isset($children['srank']) ? $children['srank']['srank'] : "-";

    return $child;
  }

  public function getChildNotRecursive($childrens, $wid, $mid)
  {

    foreach ($childrens as $key => $children) {

      $pdj = $children->load(['downline', 'srank', 'npdj' => function ($q) use ($wid, $mid) {
        return $q->where('wid', $wid)->with(['effectiveRank' => function ($query) use ($mid) {
          return $query->where('mid', $mid);
        }]);
      }]);

      $erank = ERank::where([['jbid', $children->id], ['mid', $mid]])->first();

      $child[$key]['username'] = $children['username'];
      $child[$key]['nama'] = $children->nama;
      $child[$key]['srank'] =  $this->rank_name[empty($children->srank) ? 0 : $children->srank->srank];

      if (empty($children->srank)) {
        $child[$key]['jrank'] =  "-";
      } else {
        if ($children->srank->appv >= 2400) {
          $child[$key]['jrank'] =  "γ";
        } elseif ($children->srank->appv >= 1200) {
          $child[$key]['jrank'] =  "β";
        } elseif ($children->srank->appv >= 240) {
          $child[$key]['jrank'] =  "α";
        }
      }

      $child[$key]['appv'] = empty($children->srank) ? 0 : $children->srank->appv;

      $child[$key]['ppv'] = !empty($pdj->npdj) && $pdj->npdj->count() ? $pdj->npdj->first()->ppv : 0;
      $child[$key]['pbv'] = !empty($pdj->npdj) && $pdj->npdj->count() ? $pdj->npdj->first()->pbv : 0;
      $child[$key]['gpvj'] = !empty($pdj->npdj) && $pdj->npdj->count() ? $pdj->npdj->first()->gpvj : 0;
      $child[$key]['gbvj'] = !empty($pdj->npdj) && $pdj->npdj->count() ? $pdj->npdj->first()->gbvj : 0;
      $child[$key]['qudu'] = !empty($pdj->npdj) && $pdj->npdj->count() ? $pdj->npdj->first()->qudu : 0;

      $child[$key]['pro'] = $erank ? $erank->ppv : 0; //!empty($pdj->npdj->first()->effectiveRank) ? $pdj->npdj->first()->effectiveRank[0]->ppv : 0;
      $child[$key]['erank'] = $this->rank_name[$erank ? $erank->erank : 0]; //$this->rank_name[!empty($pdj->npdj->first()->effectiveRank) ? $pdj->npdj->first()->effectiveRank[0]->erank : 0];

      $child[$key]['_hasChildren'] = $pdj->downline->count() ? true : false;
      #$child[$key]['_showChildren'] = $pdj->downline->count() ? true : false;
    }


    return $child;
  }

  public function removeSpecialCharacter($value)
  {
    //allowspace
    return strtolower(preg_replace('/[^A-Za-z0-9\- ]/', '', $value));
  }

  public function setCamelCase($value)
  {
    return ucwords(strtolower($value));
  }

  public function isDownline($username, $master)
  {
    $user = Member::where('username', $master)->first();
    $userTarget = Member::where('username', $username)->first();

    $isMember = Member::whereIn('username', $user->user->memberships->pluck('username'))->get();
    $inNetwork = false;

    if ($userTarget && $isMember) {
      if (Auth::user()->flag === 0 || $isMember || $userTarget->upid == $user->jbid) {
        $inNetwork = true;
      } elseif ($userTarget->id_upline_fk != null) {
        $upline = Member::where('id', $userTarget->upid)->first();
        $inNetwork = $this->isDownline($upline->username, $master);
      }
    }

    return $inNetwork;
  }

  public function getDownlineWhereRankAbove($id, $rank)
  {

    $downlines = User::where('id', $id)->with('srank')->get();
    foreach ($downlines as $downline) {

      $result = $this->getDownlineWhereRankAbove($downline->id, $rank);
      if (empty($result[$downline->srank->srank])) {
        $result[$downline->srank->srank] += 1;
      } else {
        $result[$downline->srank->srank] = 1;
      }
    }

    return $result;
  }

  // public function getProduct($code)
  // {

  //   $login = Auth::user();

  //   $login = $login ? $login->load('srank') : null;
  //   $srank = empty($login->srank) ? 0 : $login->srank->srank;
  //   $JPSPacked = $login ? SpecialOffer::where('jbid', $login->id)->first() : null;
  //   $category = Product::where('code', $code)->first();
  //   $category = $category ? $category->category : null;

  //   if ($code) {

  //     if ($category) {
  //       $products = Product::where([['code', $code], ['active', true], ['show', '>=', $login ? $login->flag : 2]])
  //         ->with(['barang', 'variants' => function ($query) use ($login, $JPSPacked, $srank, $category) {
  //           return $query->with(['barang_induk' => function ($barang) use ($login, $JPSPacked, $srank, $category) {
  //             if ($login && $login->flag == 0) {
  //               return $barang->where('id_category_fk', $category)->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //             } elseif ($login && $login->flag == 1) {
  //               if ($srank >= 5) {
  //                 return $barang->where('id_category_fk', $category)->where([['is_show', '>=', 1], ['id_jenis_fk', '!=', 3], ['id_jenis_fk', '!=', 6]])->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               } elseif ($JPSPacked) {
  //                 return $barang->where('id_category_fk', $category)->where([['is_show', '>=', 1], ['id_jenis_fk', '!=', 6]])->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               } else {
  //                 return $barang->where('id_category_fk', $category)->where('is_show', '>=', 1)->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               }
  //             } else {
  //               return $barang->where('is_show', $login ? $login->flag : 2)->where('id_category_fk', $category)->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //             }
  //           }]);
  //         }])->orderBy('id')->first();
  //     } else {
  //       $products = Product::where([['code', $code], ['active', true], ['show', '>=', $login ? $login->flag : 2]])
  //         ->with(['barang', 'variants' => function ($query) use ($login, $JPSPacked, $srank, $category) {
  //           return $query->with(['barang_induk' => function ($barang) use ($login, $JPSPacked, $srank, $category) {
  //             if ($login && $login->flag == 0) {
  //               return $barang->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //             } elseif ($login && $login->flag == 1) {
  //               if ($srank >= 5) {
  //                 return $barang->where([['is_show', '>=', 1], ['id_jenis_fk', '!=', 3], ['id_jenis_fk', '!=', 6]])->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               } elseif ($JPSPacked) {
  //                 return $barang->where([['is_show', '>=', 1], ['id_jenis_fk', '!=', 6]])->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               } else {
  //                 return $barang->where('is_show', '>=', 1)->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               }
  //             } else {
  //               return $barang->where('is_show', $login ? $login->flag : 2)->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //             }
  //           }]);
  //         }])->orderBy('id')->first();
  //     }
  //   } else {
  //     if ($category) {
  //       $products = Product::where([['active', true], ['show', '>=', $login ? $login->flag : 2]])
  //         ->with(['barang', 'variants' => function ($query) use ($login, $JPSPacked, $srank, $category) {
  //           return $query->with(['barang_induk' => function ($barang) use ($login, $JPSPacked, $srank, $category) {
  //             if ($login && $login->flag == 0) {
  //               return $barang->where('id_category_fk', $category)->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //             } elseif ($login && $login->flag == 1) {
  //               if ($srank >= 5) {
  //                 return $barang->where('id_category_fk', $category)->where([['is_show', '>=', 1], ['id_jenis_fk', '!=', 3], ['id_jenis_fk', '!=', 6]])->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               } elseif ($JPSPacked) {
  //                 return $barang->where('id_category_fk', $category)->where([['is_show', '>=', 1], ['id_jenis_fk', '!=', 6]])->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               } else {
  //                 return $barang->where('id_category_fk', $category)->where('is_show', '>=', 1)->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               }
  //             } else {
  //               return $barang->where('is_show', 2)->where('id_category_fk', $category)->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //             }
  //           }]);
  //         }])->orderBy('id')->get();
  //     } else {
  //       $products = Product::where([['active', true], ['show', '>=', $login ? $login->flag : 2]])
  //         ->with(['barang', 'variants' => function ($query) use ($login, $JPSPacked, $srank, $category) {
  //           return $query->with(['barang_induk' => function ($barang) use ($login, $JPSPacked, $srank, $category) {
  //             if ($login && $login->flag == 0) {
  //               return $barang->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //             } elseif ($login && $login->flag == 1) {
  //               if ($srank >= 5) {
  //                 return $barang->where([['is_show', '>=', 1], ['id_jenis_fk', '!=', 3], ['id_jenis_fk', '!=', 6]])->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               } elseif ($JPSPacked) {
  //                 return $barang->where([['is_show', '>=', 1], ['id_jenis_fk', '!=', 6]])->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               } else {
  //                 return $barang->where('is_show', '>=', 1)->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //               }
  //             } else {
  //               return $barang->where('is_show', 2)->whereIn('status', ['A', 'I', 'X'])->orderBy('index');
  //             }
  //           }]);
  //         }])->orderBy('id')->get();
  //     }
  //   }

  //   return $products;
  // }

  public function getSponsorJoybizer($spid)
  {
    $sponsor = User::where('id', $spid)->first();


    $user = null;
    if ($sponsor) {
      if ($sponsor->flag == 1 && $sponsor->status == 1) {
        $user = $sponsor;
      } else {
        $user = $this->getSponsorJoybizer($sponsor->id_sponsor_fk);
      }
    }

    return $user;
  }

  public function getUplineJoybizer($upid)
  {
    $upline = User::where('id', $upid)->first();

    $user = null;
    if ($upline) {
      if ($upline->flag == 1 && $upline->status == 1) {
        $user = $upline;
      } else {
        $user = $this->getUplineJoybizer($upline->id_upline_fk);
      }
    }

    return $user;
  }

  public function recalculateCarryForward($member_uuid, $week, $carry_week)
  {
    $pdjs = PreparedDataJoy::where([
      ['placement_uuid', $member_uuid],
      ['wid', $week]
    ])->orderBy('gpvj')->get();

    $lastCF = CarryForwardDetail::where([
      ['member_uuid', $member_uuid],
      ['wid', $carry_week]
    ])->first();

    $cf = CarryForwardDetail::where([
      ['member_uuid', $member_uuid],
      ['wid', $week]
    ])->first();

    $found = false;
    foreach ($pdjs as $pdj) {
      $dataPV[$pdj->member_uuid] = $pdj->pvj + $pdj->gpvj;
      $dataBV[$pdj->member_uuid] = $pdj->bvj + $pdj->gbvj;

      if ($lastCF->member_uuid == $pdj->member_uuid) {
        $dataPV[$pdj->member_uuid] += $lastCF->gpvj;
        $dataBV[$pdj->member_uuid] += $lastCF->gbvj;
        $found = true;
      }
    }

    if (!$found) {
      $dataPV[$lastCF->member_uuid] = $lastCF->gpvj;
      $dataBV[$lastCF->member_uuid] = $lastCF->gbvj;
    }

    arsort($dataPV);

    $cgpvj = 0;
    $cgbvj = 0;
    $ctr = 0;
    $cid = null;
    foreach ($dataPV as $key => $pv) {
      if ($ctr == 0) {
        $cid = $key;
        $cgpvj = $pv;
        $cgbvj = $dataBV[$key];
      } else {
        if ($cgpvj < $pv) $cid = $key;
        $cgpvj -= $pv;
        $cgbvj -= $dataBV[$key];
      }
      $ctr++;
    }

    $cf->member_uuid = $cid;
    $cf->gpvj = $cgpvj;
    $cf->gbvj = $cgbvj;
    $cf->save();
  }

  // public function getProductSales($transactions)
  // {
  //   $productSales = array();
  //   foreach ($transactions as $key => $transaction) {
  //     # code...
  //     foreach ($transaction->details as $key => $td) {
  //       # code...
  //       foreach ($td->barang->barang_detail as $bd) {
  //         $productID = $bd->product_uuid;
  //         $qty = $bd->qty;
  //         // $product = barang::where('id', $bd->id_barang_fk)->first();

  //         if (isset($productSales[$productID])) {
  //           $productSales[$productID]['qty'] += $bd->qty * $td->qty;
  //           $productSales[$productID]['total'] += $bd->qty * $product->harga_1;
  //         } else {
  //           $productSales[$productID]['name'] = $product->nama;
  //           $productSales[$productID]['qty'] = $bd->qty * $td->qty;
  //           $productSales[$productID]['total'] = $bd->qty * $product->harga_1;
  //         }
  //       }
  //     }
  //   }

  //   return $productSales;
  // }

  public function getLeaf($jbid, $position)
  {
    //$user = User::where('id',$jbid)->first();
    $user = Member::where('jbid', $jbid)->first();

    if ($user && $position == 'left') {
      if ($user->left) {
        $result = $this->getLeaf($user->left, $position);
      } else {
        $result = $user;
      }
    } elseif ($user && $position == 'right') {
      if ($user->right) {
        $result = $this->getLeaf($user->right, $position);
      } else {
        $result = $user;
      }
    }



    return $result;
  }


  public function pushActivePosition($srank, $rank)
  {
    $newsrank = SRank::where('jbid', $srank->upid)->first();

    if ($newsrank) {
      if ($rank == 7) {
        $srank->bj_active++;
      } elseif ($srank && $rank >= 8) {
        $srank->vj_active++;
      }
      $srank->save();
      if ($newsrank->upid != null) $this->pushActivePosition($newsrank, $rank);
    } else {
      echo $srank->jbid . " ";
    }
  }

  public function addMember(
    $member_uuid,
    $first_name,
    $last_name,
    $user_id,
    $user_uuid,
    $sponsor_id,
    $sponsor_uuid,
    $placement_id = null,
    $placement_uuid = null,
    $status = 0,
    $membership_status = 1,
    $starter = false,
    $min_bv,
    $reg_fee
  ) {

    while (Member::where('uuid', $member_uuid)->first()) {
      $member_uuid = Carbon::now()->format('ym') . $member_uuid . rand(11, 99);
    }

    $membership = Member::create([
      'uuid' => $member_uuid,
      'user_id' => $user_id,
      'user_uuid' => $user_uuid
    ]);
    $membership->uuid = Str::uuid();
    // $membership->member_uuid = Carbon::now()->format('ym') . $membership->user->id . rand(11, 99);;
    $membership->sponsor_id = $sponsor_id;
    $membership->sponsor_uuid = $sponsor_uuid;
    $membership->placement_id = $placement_id;
    $membership->placement_uuid = $placement_uuid;
    $membership->first_name = $first_name;
    $membership->last_name = $last_name;
    $membership->status = $status;
    $membership->membership_status = $membership_status;
    $membership->starter = $starter;
    $membership->min_bv = $min_bv;
    $membership->created_by = $user_uuid;
    $membership->created_at = Carbon::now();
    $membership->save();

    return $membership;
  }

  public function getDownlineStatus($member_uuid)
  {

    $downlines = Member::where('placement_uuid', $member_uuid)->get();
    foreach ($downlines as $downline) {
      Log::info($downline->uuid . ";" .
        $downline->first_name . ";" .
        $downline->phone . ";" .
        ($downline->status ? 'Dormant at ' . $downline->will_dormant_at : 'Active'));
      $result = $this->getDownlineStatus($downline->member_uuid);
    }

    return $result;
  }
}
