<?php

namespace App\Helpers\Bonuses;

use DB;
use Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class JoyHelper extends Model
{
    //    
    public function syncJoyData($date)
    {
        Log::info('Sycn Joy Data ' . $date . ' at ' . Carbon::now());
        $transactions = transaksi::where('transaction_date', $date)->whereIn('status', ['S', 'A', 'PC', 'I'])->with('user')->get();
        foreach ($transactions as $key => $t) {
            echo $key;
            Log::debug($t->code_trans);
            Log::debug("-------------");
            if ($t->bv_total > 0 || $t->rv_total > 0) {
                $user = Membership::where('jbid', $t->id_cust_fk)->with('srank')->first();
                Log::debug($user->username);
                if (isset($user->srank) && $user->srank->appv >= 2400) {
                    $jrank = 3;
                } elseif (isset($user->srank) && $user->srank->appv >= 1200) {
                    $jrank = 2;
                } elseif (isset($user->srank) && $user->srank->appv >= 240) {
                    $jrank = 1;
                } else {
                    $jrank = 0;
                }

                $joydata = JoyData::firstOrCreate(['date' => $t->transaction_date, 'jbid' => $user->jbid]);
                $joydata->spid = $user->spid;
                $joydata->upid = $user->upid;

                $joydata->ppv += $t->pv_plan_joy;
                $joydata->pbv += $t->bv_plan_joy;
                $joydata->prv += $t->rv_plan_joy;

                $joydata->pgpv += $t->pv_plan_joy;
                $joydata->pgbv += $t->bv_plan_joy;
                $joydata->pgrv += $t->rv_plan_joy;

                $joydata->jrank = $jrank;
                $joydata->save();

                Log::debug("-----------");
                if (!is_null($t->membership->upid)) $this->pushJoyData($t->transaction_date, $t->membership->upid, $t->pv_plan_joy, $t->bv_plan_joy, $t->rv_total);
                Log::debug("-----------");
            }
            // Log::debug($t->membership->upid);
        }
        Log::info('Sycn Joy Data ' . $date . 'finish at ' . Carbon::now());
    }

    public function syncJoyDatabyCode($code)
    {
        #Log::info('Sycn Joy Data '.$date.' at '.Carbon::now());      
        $t = transaksi::where('code_trans', $code)->whereIn('status', ['S', 'A', 'PC', 'I'])->with('user')->first();
        #foreach ($transactions as $key => $t) {
        #Log::debug($t->code_trans);
        if ($t->bv_total > 0 || $t->rv_total > 0) {
            $user = Membership::where('jbid', $t->id_cust_fk)->with('srank')->first();
            if (isset($user->srank) && $user->srank->appv >= 2400) {
                $jrank = 3;
            } elseif (isset($user->srank) && $user->srank->appv >= 1200) {
                $jrank = 2;
            } elseif (isset($user->srank) && $user->srank->appv >= 240) {
                $jrank = 1;
            } else {
                $jrank = 0;
            }

            $joydata = JoyData::firstOrCreate(['date' => $t->transaction_date, 'jbid' => $user->jbid]);
            $joydata->spid = $user->spid;
            $joydata->upid = $user->upid;
            $joydata->ppv += $t->pv_plan_joy;
            $joydata->pbv += $t->bv_plan_joy;
            $joydata->prv += $t->rv_plan_joy;

            $joydata->pgpv += $t->pv_plan_joy;
            $joydata->pgbv += $t->bv_plan_joy;
            $joydata->pgrv += $t->rv_plan_joy;

            $joydata->jrank = $jrank;
            $joydata->save();

            if (!is_null($t->membership->upid)) $this->pushJoyData($t->transaction_date, $t->membership->upid, $t->pv_plan_joy, $t->bv_plan_joy, $t->rv_total);
        }
        #Log::debug($t->user->upid);
        #}
        #Log::info('Sycn Joy Data '.$date.'finish at '.Carbon::now());      
    }



    public function pushJoyData($date, $upid, $gpv, $gbv, $grv)
    {
        $user = Membership::where('jbid', $upid)->with('srank')->first();
        // Log::debug($user->username);
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

            $joydata = JoyData::firstOrCreate(['date' => $date, 'jbid' => $user->jbid]);
            $joydata->spid = $user->spid;
            $joydata->upid = $user->upid;
            $joydata->gpv += $gpv;
            $joydata->gbv += $gbv;
            $joydata->grv += $grv;
            $joydata->pgpv += $gpv;
            $joydata->pgbv += $gbv;
            $joydata->pgrv += $grv;
            $joydata->jrank = $jrank;
            $joydata->save();

            if ($user->upid != null) $this->pushJoyData($date, $user->upid, $gpv, $gbv, $grv);
        }
    }

    public function clearJoyData($date)
    {
        echo "Start Clear Data \n";
        $joydatas = JoyData::where('date', $date)->update(['ppv' => 0, 'pbv' => 0, 'prv' => 0, 'gpv' => 0, 'gbv' => 0, 'grv' => 0, 'pgpv' => 0, 'pgbv' => 0, 'pgrv' => 0]);
        echo "End Clear Data \n";
    }

    public function deleteJoyData($date)
    {
        $joydatas = JoyData::where('date', $date)->delete();
    }

    public function pushNewABGCount($jbid, $year, $month, $rank)
    {

        $user = Membership::where('jbid', $jbid)->first();
        if ($jbid && $user) {
            $vs = \App\VitalSign::firstOrCreate(['owner' => $user->uid, 'year' => $year, 'month' => $month]);
            $vs->abg++;

            if ($rank == 1) {
                $vs->a++;
            } else if ($rank == 2) {
                $vs->b++;
            } else if ($rank >= 3) {
                $vs->g++;
            }

            $vs->save();

            if ($user->upid) {
                $this->pushNewABGCount($user->upid, $year, $month, $rank);
            }
        }
    }


    public function checkAtLeg($master, $target, $id)
    {
        $user = Membership::where('jbid', $id)->first();

        if ($user->upid == $target) {
            $result = true;
        } else if ($user->upid == $master || $user->upid == null) {
            $result = false;
        } else if ($user->upid != null) {
            $result = $this->checkAtLeg($master, $target, $user->upid);
        }

        return $result;
    }

    public function checkYoungEagle($uid, $mid)
    {
        // $erank = erank::where('mid',$mid)->get();
        // $pbv = $erank->sum('ppv');

        // $sponsor = User::where('uid',$uid)->with('srank')->get();

        $user = User::where('uid', $uid)->with(['srank', 'vital_signs'])->first();
        $gammaChilds = srank::where([['spid', $user->id], ['srank', '>=', 3]])->get();

        $gammaAtLeft = false;
        $gammaAtRight = false;

        foreach ($gammaChilds as $key => $child) {
            $gammaAtLeft = $this->checkAtLeg($user->id, $user->left, $child->user->id);
            if (!$gammaAtLeft) $gammaAtRight = true;
        }



        if ($user->srank >= 3) {
            $sponsorGamma = srank::where([['spid', $user->id], ['srank', 3]])->get();
        }
    }

    public function resetCarryForward($user)
    {
        $jcf = JoyCarryForward::where('owner', $user->uid)->orderBy('date', 'DESC')->first();
        if ($jcf) {
            $jcf->big_bv = 0;
            $jcf->small_bv = 0;
            $jcf->save();
        }

        $jrv = JoyRVForward::where('owner', $user->uid)->orderBy('date', 'DESC')->first();
        if ($jrv) {
            $jrv->big_rv = 0;
            $jrv->small_rv = 0;
            $jrv->save();
        }

        $jpr = JoyPointReward::where('owner', $user->uid)->delete();
    }

    public function diffMonth($sdate, $edate)
    {
        $to = Carbon::parse($sdate);
        $from = Carbon::parse($edate);
        $result = $to->diffInMonths($from);

        return $result;
    }

    public function dormant($uid)
    {

        // $user = Membership::where('uid',$uid)->with('srank')->first();
        $user = User::where('uid', $uid)->with(['srank', 'memberships'])->first();

        $now = Carbon::now()->toDateString();
        $dormant = Dormant::where('owner', $uid)->first();

        if ($dormant) {
            if ($now == $dormant->will) {
                $user->dormant = $dormant->will;
                $user->save();

                // $reset = $this->resetCarryForward($user);

                foreach ($user->memberships as $membership) {
                    $membership->dormant = $dormant->will;
                    $membership->save();

                    $reset = $this->resetCarryForward($membership);
                }
            } else if ($dormant->will > $now && $user->dormant) {
                $user->dormant = null;
                $user->save();

                foreach ($user->memberships as $membership) {
                    $membership->dormant = null;
                    $membership->save();
                }
            } else if ($dormant->will < $now && $user->dormant == null) {
                $user->dormant = $dormant->will;
                $user->save();

                // $reset = $this->resetCarryForward($user);
                foreach ($user->memberships as $membership) {
                    $membership->dormant = $dormant->will;
                    $membership->save();

                    $reset = $this->resetCarryForward($membership);
                }
            }
        }


        // $e6date = Carbon::now()->subMonths(6)->toDateString();        
        // $e12date = Carbon::now()->subMonths(12)->toDateString();        
        // $diff = $this->diffMonth($user->activated_at,$now);        

        // $endDate = $diff <= 12 ? $e12date : $e6date;
        // $sixMonthsBV = transaksi::where('id_cust_fk',$user->id)->whereBetween('transaction_date',[$endDate,$now])->sum('bv_total');

        // if($sixMonthsBV < 100 && $diff > 12){
        //     if($user->dormant == null){
        //         $user->dormant = $now;
        //         $user->save();

        //         $reset = $this->resetCarryForward($user);
        //     }
        // } else {
        //     $user->dormant = null;
        //     $user->save();
        // }                 

    }

    public function downgradeToSC($uid)
    {

        try {
            DB::beginTransaction();
            $u = User::where('uid', $uid)->first();

            if ($u) {
                $email = $u->email;
                $nik = $u->no_ktp;
                $username = $u->username;
                $name = $u->nama;
                $password = $u->password;

                $u->nama = "-";
                $u->password = "1266hwehfsweo34iu437797dhfkjsdhfshf";
                $u->username = "dgsc-" . $u->username;
                $u->email = "dgsc-" . $u->email;
                $u->no_ktp = null;
                // $u->upid = null;       
                // $u->disabled = 1;
                $u->dormant = null;
                $u->save();

                $childs = User::where('id_sponsor_fk', $u->id)->update(['id_sponsor_fk' => $u->id_sponsor_fk]);

                $new = $u->replicate()->fill([
                    'id' => DB::table('users')->max('id') + 1,
                    'nama' => $name,
                    'username' => $username,
                    'password' => $password,
                    // 'upid' => null,
                    'no_ktp' => $nik,
                    'email' => $email,
                    'flag' => 2,
                    'uid' => Str::uuid(),
                    'left' => null,
                    'right' => null,
                ]);
                $new->save();
            }


            #membership
            $m = Membership::where('uid', $uid)->first();
            $username = $m->username;
            $membership_jbid = $m->jbid;
            $membership_childs = Membership::where('spid', $m->jbid)->update(['spid' => $m->spid]);

            $newMembership = $m->replicate()->fill([
                'id' => DB::table('memberships')->max('id') + 1,
                'owner' => $new->uid,
                'username' => $username,
                'jbid' => $membership_jbid . "000",
                'upid' => null,
                'flag' => 2,
                'dormant' => null,
                'uid' => Str::uuid(),
                'left' => null,
                'right' => null,
            ]);

            $newMembership->save();

            $m->username = "dgsc-" . $m->username;
            $m->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            // print_r($th);
            echo $th;
        }
    }
}
