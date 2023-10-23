<?php

namespace App\Helpers\Bonuses;

use App\Models\Bonuses\Ranks\SRank;
use App\Models\Calculations\Bonuses\Period;
use App\Models\Orders\Production\OrderHeader;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Challenge extends Model
{
    public function YoungEagle($member_uuid)
    {

        $now = Carbon::now();
        $helper = new Helper;

        //$sMonth = 4;
        $periode = $helper->getPeriodeMonthBetween(5);

        $sDate = $periode["sDate"];
        $eDate = $periode["eDate"];

        /* 
        $qualification = User::where('id_sponsor_fk',$member_uuid)->whereBetween('activated_at',[$sDate,$eDate])->whereHas('srank',function($q){
            $q->where('srank','>=',3);
        })->count();
        */
        $qualification = 0;
        $sponsors = User::where('id_sponsor_fk', $member_uuid)->whereHas('srank', function ($q) {
            $q->where('srank', '>=', 3);
        })->get();

        foreach ($sponsors as $sponsor) {
            $qualification += $this->checkJPSYoungEagle($sponsor->id, $sDate, $eDate);
        }

        return $qualification;
    }

    public function YoungEagleDetail($member_uuid)
    {

        $now = Carbon::now();
        $helper = new Helper;

        $week = Period::where([['start_date', '<=', $now], ['end_date', '>=', $now]])->first();

        $month = date('m', strtotime($week->end_date));
        $year = $now->year;

        $qualifications = array();

        $m = $month;

        for ($i = 0; $i < 5; $i++) {

            $m = $m == 0 ? 12 : $m;
            if ($m == 12) $year--;

            $month_name = $helper->getMonthName($m);

            if ($m) {
                $periode = $helper->getPeriodeMonth($m, $year);
                $sDate = $periode["start_date"];
                $eDate = $periode["end_date"];


                $sponsors = User::where('sponrsor_uuid', $member_uuid)
                    ->whereHas('srank', function ($q) {
                        $q->where('srank', '>=', 3);
                    })->get();

                $JPSYE = 0;
                foreach ($sponsors as $key => $sponsor) {

                    $JPSYE = $this->checkJPSYoungEagle($sponsor->id, $sDate, $eDate);
                    if (!$JPSYE) {
                        $sponsors->forget($key);
                    }
                }


                array_push($qualifications, ['month' => $m, 'year' => $year, 'month_name' => $month_name, 'counter' => $sponsors->count(), 'user' => $sponsors]);
            }

            $m--;
        }


        return $qualifications;
    }

    public function checkJPSYoungEagle($member_uuid, $sDate, $eDate)
    {
        $JPSYE = 0;
        $trxs = OrderHeader::where('member_uuid', $member_uuid)
            // ->whereIn('status', ['PC', 'S', 'A', 'I'])
            ->orderBy('transaction_date', 'asc')
            ->get();

        $totalPPV = 0;
        $dateJPS = "";
        foreach ($trxs as $trx) {
            $totalPPV += $trx->pv_total;
            if ($totalPPV >= 400) {
                $dateJPS = $trx->transaction_date;
                break;
            }
        }

        if ($dateJPS >= $sDate && $dateJPS <= $eDate) {
            $JPSYE = 1;
        }

        return $JPSYE;
    }

    public function getAppvDate($member_uuid, $appv)
    {
        $JBP = 0;
        $trxs = OrderHeader::where('member_uuid', $member_uuid)
            // ->whereIn('status', ['PC', 'S', 'A', 'J', 'I', 'X'])
            ->orderBy('transaction_date', 'asc')
            ->get();

        $totalPPV = 0;
        $dateJBP = "";
        foreach ($trxs as $trx) {
            $totalPPV += $trx->pv_total;
            if ($totalPPV >= $appv) {
                $dateJBP = $trx->transaction_date;
                break;
            }
        }

        return $dateJBP;
    }

    public function JBPList($sDate, $eDate)
    {
        $JBPAboveLists = SRank::where('srank', '>=', 5)
            // ->with('user')
            ->with('member')
            ->get();
        foreach ($JBPAboveLists as $JBP) {
            $dateReachAPPV = $this->getAppvDate($JBP->member_uuid, 2000);
            if ($dateJPS < $sDate && $dateJPS > $eDate) {
                $JBPAboveLists->forget($key);
            }
        }
        return $JBPAboveLists;
    }


    public function PointCenturionList()
    {

        $data = array();

        $users = User::where([['flag', 1], ['status', 1]])
            ->with('point_centurion')
            ->get();
        foreach ($users as $user) {
            $pc = 0;
            $pc = $user->point_centurion->count() ? $user->point_centurion->sum('debit') : 0;

            if ($pc > 0) array_push($data, ['username' => $user->username, 'name' => $user->nama, 'hp' => $user->hanphone, 'pc' => $pc]);
        }

        return $data;
    }
}
