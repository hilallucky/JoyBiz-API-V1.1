<?php

namespace App\Services\Calculations\Transactions;

use app\Libraries\Core;
use App\Models\Calculations\Bonus\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class PaymentApprovalService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }
//fuction to update transaction if payment valid
public function confirmPaymentValid($code, $date){
    $money = new Money;
    $sms = new \App\AlvaMarvello\SMS;
    $joyhelper = new \App\AlvaMarvello\JoyHelper;

    $userlogin = Auth()->user();		
    $trx = transaksi::where('code_trans',$code)->whereIn('status',['P','WP','CP','COD'])->with(['transaksi_detail','province','user'])->first();				
    

    /*joybiz v1
    //paket Registrasi SC yang mengandung biaya reg dan selisih
    $scRegisterSpecialCase = array('RSC01','RSC02');
    $scRegisterSpecialCase2 = array('RSC04');
    */

    if ($trx){
        $trx->transaction_date = $date;
        $trx->save();
        
        $user = $trx->id_sc_fk ? 
                Membership::where('jbid',$trx->id_sc_fk)->with(['sponsor','srank'])->first() : 
                Membership::where('jbid',$trx->id_cust_fk)->with(['sponsor','srank'])->first();
        $membership = Membership::where('jbid',$trx->id_cust_fk)->first();

        $srank = isset($user->srank) ? $user->srank : null;

        $indent = false;
        $totalHargaWIB = 0;
        $selisihRetail = 0;
        $hasRegister = false;
        $qudu = 0;
        $quduBVG = 0;
        $hargaAsliCod = 0;

        // foreach ($trx['transaksi_detail'] as $trxd){
        //     $product_indent = \App\Models\barang::
        //         where([['id',$trxd->id_barang_fk],['status','I']])
        //         ->with('barang_detail')->first();
        //     $indent = isset($product_indent) ? true : false;

        //     $product = barang::where('id',$trxd->id_barang_fk)->first();				
        //     if($trx->cod) $hargaAsliCod += $product->harga_1 * $trxd->qty;

        //     #if upgrade to joybizer
        //     if( ($product->id == 87 || $product->is_register == 1) && $user->flag == 2){
        //         $user->flag = 1;
        //         $user->activated_at = $trx->transaction_date;
        //         $user->save();
                
        //         $trx->id_cust_fk = $trx->id_sc_fk;
        //         $trx->id_sc_fk = null;
        //         $trx->save();
        //     } else if ($product->id == 88 && $user->flag == 1){
        //         $result = $joyhelper->downgradeToSC($user->uid);
        //     } else if($product->id == 2 && $user->flag != 2){
        //         $user->flag = 2;
        //         $user->activated_at = $trx->transaction_date;
        //         $user->upid= null;
        //         $user->status = 1;
        //         $user->save();	
                
        //         $parent = User::where('uid',$user->owner)->first();
        //         $parent->flag = 2;
        //         $parent->activated_at = $trx->transaction_date;
        //         $parent->id_upline_fk= null;
        //         $parent->status = 1;
        //         $parent->save();
                                    
        //         #$trx->id_sc_fk = $trx->id_cust_fk;
        //         #$trx->save();
        //     }
        //     #endif
            
        //     #before if product qudu maka point qudu bertambah 1
        //     #after if product xpress jps maka point qudu bertambah 1
        //     if($product->id_jenis_fk == 6 || $product->id_jenis_fk == 8) {
        //         $qudu += $trxd->qty;
        //         $quduBVG += $trxd->qty * $product->pv;
        //     }

        //     //if ($trxd->id_barang_fk <= 2){
        //     if ($product->is_register == 1){	

        //         $parent = User::where('uid',$trx->membership->owner)->first();
        //         $parent->flag = 1;
        //         $parent->activated_at = $parent->status == 1 ? $parent->activated_at : $trx->transaction_date;
        //         $parent->status = 1;										
        //         $parent->save();
                
        //         $membership->flag = $product->is_register;
        //         $membership->activated_at = $trx->transaction_date;
        //         $membership->status = 1;

        //         $membership->save();							
                
        //         $message_user = "Selamat! membership anda telah aktif. Login dengan ".$user->email." & password yg didaftarkan. URL referral anda: www.joybiz.co.id/".$user->username;
                
        //         #$message_sponsor = "Selamat! ".$user->username." yang anda Sponsori telah aktif. Lakukan pengaturan upline di menu placement. Atau berlaku placement otomatis setiap hari kamis";
        //         $message_sponsor = "Selamat! ".$user->username." yang anda Sponsori telah aktif. Lakukan pengaturan upline di menu placement. Atau berlaku placement otomatis setiap hari jam 23.59 WIB";

        //         /*
        //         if($trx->id_sc_fk){
        //             $message_user = "Selamat! anda telah menjadi Special Customer kami. Login dengan ".$user->email." & password yg didaftarkan di www.joybiz.co.id/";
        //             $message_sponsor = "Selamat! Special Customer ".$user->username." yang anda Sponsori telah aktif.";

        //             //$telegram = new TelegramBot;
        //             //$telegram_message = "Special Customer ".$user->nama." yang disponsori oleh ".$user->sponsor->nama. " telah diaktifkan";
        //             //$telegram_result = $telegram->toIT($telegram_message);
        //             //$telegram_result = $telegram->toExecutive($telegram_message);
        //         } else {
        //             $message_user = "Selamat! membership anda telah aktif. Login dengan ".$user->email." & password yg didaftarkan. URL referral anda: www.joybiz.co.id/".$user->username;
        //             $message_sponsor = "Selamat! ".$user->username." yang anda Sponsori telah aktif. Lakukan pengaturan upline di menu placement. Atau berlaku placement otomatis setiap hari kamis";
        //         }
        //         */

        //         // $destination_user = $user->handphone;
        //         // $result_user = $sms->send($destination_user,$message_user);

        //         // $destination_sponsor = $membership->sponsor->user->handphone;
        //         // $result_sponsor = $sms->send($destination_sponsor,$message_sponsor);

        //         $hasRegister = true;
        //     } else if ($product->pv > 0) {
        //         $totalHargaWIB += $trxd->qty * $product->harga_1;
        //         if($user->flag == 3){
        //             $zone = $trx->province->kelompok ?? 3;
        //             $selisihRetail = $product['harga_retail_'.$zone] - $product['harga_'.$zone];						
        //         }
        //     }
        // }

        
        
        #jika transaksi sc terdapat paket register joybizer makan transaksi tersebut di rubah menjadi transaksi joybizer
        if($trx->id_sc_fk && $hasRegister){
            $trx->id_cust_fk = $trx->id_sc_fk;
            $trx->id_sc_fk = null;
        }

        $trx->transaction_date = $date;
        if($trx->status == 'COD'){				
            $trx->status = 'S';
        } else {
            $trx->status = $indent ? 'I' : 'PC';
        }
        $trx->approved_by = $userlogin ? $userlogin->id : null;
        $saved = $trx->save(); 
        
        if($trx->pv_total >= 100 && $user->dormant){
            // $user->dormant = null;
            // $user->save();

            $main_user = User::where('uid',$user->owner)->with('memberships')->first();
            if($main_user){
                $main_user->dormant = null;
                $main_user->save();

                foreach($main_user->memberships as $membership){
                    $membership->dormant = null;
                    $membership->save();                    
                }

                $dormant = \App\Dormant::where('owner',$main_user->uid)->first();
                $dormant->will = Carbon::parse($trx->transaction_date)->addMonths(6)->toDateString();
                $dormant->save();
            }
            
        }

        
        if($membership->status != 1 
            && $trx->bv_total >= $membership->min_bv 
            && $membership->user->status == 1 ){
            $membership->flag = 1;
            $membership->activated_at = $membership->status == 1 && $trx->pv_total >= $mebership->min_bv 
                                        ? $membership->activated_at 
                                        : $trx->transaction_date;
            $membership->status = 1;
            $membership->save();		
        }

        //jika transaksi Special Customer
        if($trx->id_sc_fk){
            $SCTrx = $this->calculateSCAmount($trx->code_trans);
            
            if($trx->pv_total > 0){
                                    
                if($user->flag == 3 || $hasRegister){						
                    
                    //if Customer buy with retail once change to SC
                    $user->flag = 2;
                    $user->status = 1;
                    $user->activated_at = $date;											
                    $user->save();

                    #jika customer tidak upgrade membership
                    if($user->flag == 2){
                        $message_user = "Selamat! anda telah menjadi Special Customer kami. Login dengan ".$user->email." & password yg didaftarkan di www.joybiz.co.id/";
                        $message_sponsor = "Selamat! Special Customer ".$user->username." yang anda Sponsori telah aktif.";

                        $destination_user = $user->handphone;
                        $result_user = $sms->send($destination_user,$message_user);

                        // $destination_sponsor = $user->sponsor->handphone;
                        $destination_sponsor = $membership->sponsor->user->handphone;
                        $result_sponsor = $sms->send($destination_sponsor,$message_sponsor);
                    }
                }	
                
                //$RealBV = ($trx->bv_total * 0.875);				
                //$Cashback = $totalHargaWIB * 0.083;

                $RealBV = $SCTrx['SBV'];
                $RealPV = $SCTrx['SPV'];
                $Cashback = $SCTrx['Cashback'];

                //$Cashback = $SH1 * 0.083;

                //if ($Cashback && !$voucher_amount){
                if ($Cashback){						
                    $owner = Membership::where('jbid',$trx->id_sc_fk)->first();
                    $note = "Cashback from your transaction ".$trx->code_trans;
                    $TransferCashback = $money->topupVoucher($owner->uid,$Cashback,$note,$userlogin);
                }

                $selisihRetail -= 20000; 
                $selisihRetail = $selisihRetail > 0 ? $selisihRetail : 0;					
                if ($selisihRetail){						
                    $owner = Membership::where('jbid',$trx->id_cust_fk)->first();
                    $note = "Cashback from Special Customer Registration ".$trx->code_trans;
                    $TransferCashback = $money->topupVoucher($owner->uid,$selisihRetail,$note,$userlogin);
                }
            } 
            
            /* joybiz v1 
            else if($product->pv > 0 && $product->is_register){
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

        //jika cod kasi voucher ke sponsor
        if($trx->cod){
            // $owner = User::where('id',$trx->id_cust_fk)->first();	
            $owner = Membership::where('jbid',$trx->id_cust_fk)->first();	
            $fee = ($trx->purchase_cost + $trx->shipping_cost) * 0.03;
            $codCashback = ($trx->purchase_cost - $hargaAsliCod) - $fee;

            $year = Carbon::now()->year;
            $totalBonus = \App\BonusWeekly::where([['owner',$owner->uid],['year',$year]])->get()->sum('total');
            $totalBonus += \App\BonusMonthlySummary::where([['owner',$owner->uid],['year',$year]])->get()->sum('total');
            $totalBonus += \App\PointCenturionWinner::where([['owner',$owner->uid]])->get()->sum('amount');
            $totalBonus += \App\JoyBonusSummary::where([['owner',$owner->uid],['confirmed',true]])->whereYear('date',$year)->get()->sum('total');

            $tax = new TaxID;
            $pph = round($tax->getTaxAmount($totalBonus,$codCashback));            
            if (!$owner->no_npwp) $pph += $pph * 0.2;				
            $codCashback -= $pph;

            $note = "Cashback dari transaksi COD ".$trx->code_trans." sebesar ".number_format($codCashback);
            $TransferCashbackCod = $money->topupVoucher($owner->uid,$codCashback,$note,$userlogin);

            $codProfit = CodProfit::create(['owner' => $owner->uid, 'code' => $trx->code_trans, 'member_price' => $hargaAsliCod, 'retail_price' => $trx->purchase_cost, 'cod_fee' => $fee, 'pph' => $pph, 'voucher' => $codCashback, 'vouchered'=>$TransferCashbackCod ]);
            //$trx->cod_voucher = $TransferCashbackCod;
            $trx->save();
        }

        //abodemen
        $transaction = new Transaction;
        $result = $transaction->generateAbodemenChild($trx->code_trans);

        #$userCoupon = User::where('id',$trx->id_cust_fk)->first();
        #$RewardCoupon = $this->monthyRewardCoupon($userCoupon);
        
        if($trx->is_pickup){
            $trx->pickup_code = encrypt(rand(111111,999999));
            $trx->save();
        }


        if($saved){
            
            #cashback
                foreach ($trx['transaksi_detail'] as $trxd){
                    
                    // if($trxd->id_barang_fk == 720){
                    // 	$product = barang::where('id',$trxd->id_barang_fk)->first();
                    // 	$cashback_amount = 90000 * $trxd->qty;
                    // 	$description = "Cashback ".$product->nama." dari Transaksi ".$trx->code_trans;

                    // 	$money->money("cashback",$membership, $cashback_amount, false, $description,false,$trx->code_trans);
                    // } else if($trxd->id_barang_fk == 721){
                    // 	$product = barang::where('id',$trxd->id_barang_fk)->first();
                    // 	$cashback_amount = 45000 * $trxd->qty;
                    // 	$description = "Cashback ".$product->nama." dari Transaksi ".$trx->code_trans;

                    // 	$money->money("cashback",$membership, $cashback_amount, false, $description,false,$trx->code_trans);
                    // }

                    $product = barang::where('id',$trxd->id_barang_fk)->first();
                    if($product->cashback_gamma > 0 && $srank && $srank->srank >= 3){							
                        $cashback_amount = $product->cashback_gamma * $trxd->qty;
                        $description = "Cashback ".$product->nama." dari Transaksi ".$trx->code_trans;
                        
                        //money($type,$user, $amount, $credit, $description,$freeze=false,$transaction_code = null)
                        $money->money("cashback",$membership, $cashback_amount, false, $description,false,$trx->code_trans);
                    }
                }
            #cashback
            
            $jbid = $trx->id_cust_fk;

            $ppv = $trx->pv_total;
            $pbv = $trx->bv_total;
            $prv = $trx->rv_total;
            
            $gpv = 0;
            $gbv = 0;
            $grv = 0;

            $jbp = 0;
            $bj = 0;
            $vj = 0;

            #$ppvj = $trx->pv_plan_joy;
            $ppvj = $trx->pv_plan_biz; #$trx->pv_total;
            #$pbvj = $trx->bv_plan_joy;
            $pbvj = $trx->bv_plan_biz; #$trx->bv_total;
            $prvj = $trx->rv_plan_biz; #$trx->bv_total;
            $gpvj = 0;
            $gbvj = 0;
            $grvj = 0; 

            $ppvb = 0; #$trx->pv_plan_biz;
            $pbvb = 0; #$trx->bv_plan_biz;
            $gpvb = 0;
            $gbvb = 0;

            $omzet = $trx->purchase_cost;
            $omzet_joy = $trx->price_joy;
            $omzet_biz = $trx->price_biz;
            $omzet_with_bv = $trx->omzet_with_bv ? ($trx->omzet_with_bv / 20000) : ($omzet_joy + $omzet_biz)/20000;

            if($pbv) $this->update_Effective_Rank_Sim($jbid, $ppv, $pbv, $gpv, $gbv, $date, $jbp, $bj, $vj, $ppvj, $pbvj, $gpvj, $gbvj, $ppvb, $pbvb, $gpvb, $gbvb, $omzet, $omzet_joy, $omzet_biz, $omzet_with_bv, $qudu, $quduBVG, $prvj,$grvj,$prv,$grv);

            #$reset = $joyhelper->clearJoyData($now);
            $result = $joyhelper->syncJoyDatabyCode($code);
        }

        $status = true;
        $message = "Transaction ".$trx->code_trans." settled with success!!";

    } else {

        $status = false;
        $message = "Transaction not found!!";

    }

    $result = ['status' => $status, 'message' => $message];
    return $result;
}
   
}
