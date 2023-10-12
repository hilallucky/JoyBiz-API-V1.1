<?php

namespace App\Services\Orders;

use app\Libraries\Core;
use App\Models\Members\Member;
use App\Models\Orders\OrderStatuses;
use App\Models\Orders\Production\OrderDetail;
use App\Models\Orders\Production\OrderHeader;
use App\Models\Orders\Production\OrderPayment;
use App\Models\Orders\Production\OrderShipping;
use App\Models\Orders\Temporary\OrderHeaderTemp;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderApprovalService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    public function approveOrder($request)
    {
        try {
            $uuids = $request->uuids;

            DB::beginTransaction();

            // Check Auth & update user uuid to deleted_by
            $user =  null;
            if (Auth::check()) {
                $auth = Auth::user();
                $user = $auth->uuid;
            }

            $orderTemp = OrderHeaderTemp::with('details', 'payments', 'shipping')
                ->whereIn('uuid', $uuids)
                ->where('status', 0)
                ->get();

            // ->each(function ($orderHeaderTemps) use ($user) {
            //     $orderHeader = $orderHeaderTemps->replicate();

            //     // $orderHeader->setTable('order_headers');
            //     // $orderHeader->uuid = Str::uuid()->toString();
            //     // $orderHeader->order_header_temp_uuid = $orderHeaderTemps->uuid;
            //     // $orderHeader->created_by = $user;
            //     // $orderHeader->created_by = $user;
            //     // $orderHeader->save();

            //     $details = $orderHeaderTemps->details;
            //     // $newDetails = [];
            //     foreach ($details as $detail) {
            //         $details->uuid = Str::uuid()->toString();
            //         $details->order_details_temp_uuid = $detail->uuid;
            //         $details->order_header_uuid = $orderHeader->uuid;
            //         $details->created_by = $user;
            //         $details->created_by = $user;
            //     }
            //     $orderDetail = new OrderDetail();
            //     $orderDetail->insert($details);

            // });

            // Compare the count of found UUIDs with the count from the request array
            if (
                !$orderTemp ||
                (count($orderTemp) !== count($uuids))
            ) {
                return response()->json(
                    ['message' => 'Orders fail to approve, because invalid uuid(s)'],
                    400
                );
            }

            foreach ($orderTemp as $order) {
                // New Order Header;
                $newOrderHeader = [
                    'uuid' => Str::uuid()->toString(),
                    'order_header_temp_uuid' => $order->uuid,
                    'price_code_uuid' => $order->price_code_uuid,
                    'member_uuid' => $order->member_uuid,
                    'remarks' => $order->remarks,
                    'total_discount_value' => $order->total_discount_value,
                    'total_discount_value_amount' => $order->total_discount_value_amount,
                    'total_price_after_discount' => $order->total_price_after_discount,
                    'total_amount' => $order->total_amount,
                    'total_shipping_charge' => $order->total_shipping_charge,
                    'total_payment_charge' => $order->total_payment_charge,
                    'total_amount_summary' => $order->total_amount_summary,
                    'total_pv' => $order->total_pv,
                    'total_xv' => $order->total_xv,
                    'total_bv' => $order->total_bv,
                    'total_rv' => $order->total_rv,
                    'status' => 1,
                    'airway_bill_no' => $order->airway_bill_no,
                    'created_by' => $user,
                ];

                // Update status in order_header_temp
                OrderHeaderTemp::where('uuid', $order->uuid)
                    ->update([
                        'status' => 1,
                        'updated_by' => $user
                    ]);

                // Insert into order_headers
                $orderHeader = new OrderHeader($newOrderHeader);
                $orderHeader->save();

                $newOrderHeaders[] = $newOrderHeader;

                // New Order Details
                $orderDetails = $order->details;

                foreach ($orderDetails as $orderDetail) {
                    $newOrderDetail = [
                        'uuid' => Str::uuid()->toString(),
                        'order_header_uuid' => $orderHeader->uuid,
                        'order_details_temp_uuid' => $orderDetail->uuid,
                        'product_price_uuid' => $orderDetail->product_price_uuid,
                        'qty' => $orderDetail->qty,
                        'price' => $orderDetail->price,
                        'discount_type' => $orderDetail->discount_type,
                        'discount_value' => $orderDetail->discount_value,
                        'discount_value_amount' => $orderDetail->discount_value_amount,
                        'price_after_discount' => $orderDetail->price_after_discount,
                        'pv' => $orderDetail->pv,
                        'xv' => $orderDetail->xv,
                        'bv' => $orderDetail->bv,
                        'rv' => $orderDetail->rv,
                        'status' => $orderDetail->status,
                        'created_by' => $user,
                    ];

                    // Insert into order_details
                    $orderDetails = new OrderDetail($newOrderDetail);
                    $orderDetails->save();
                    $newOrderDetails[] = $newOrderDetail;
                }

                // New Order Payments
                $orderPayments = $order->payments;

                foreach ($orderPayments as $orderPayment) {
                    $newOrderPayment = [
                        'uuid' => Str::uuid()->toString(),
                        'order_payments_temp_uuid' => $orderPayment->uuid,
                        'order_header_uuid' => $orderHeader->uuid,
                        'payment_type_uuid' => $orderPayment->payment_type_uuid,
                        'total_amount' => $orderPayment->total_amount,
                        'total_discount' => $orderPayment->total_discount,
                        'total_amount_after_discount' => $orderPayment->total_amount_after_discount,
                        'remarks' => $orderPayment->remarks,
                        'created_by' => $user,
                    ];

                    // Insert into order_payments
                    $orderPayments = new OrderPayment($newOrderPayment);
                    $orderPayments->save();

                    $newOrderPayments[] = $newOrderPayment;
                }

                // // New Order Shipping
                $orderShipping = $order->shipping;

                foreach ($orderShipping as $shipping) {
                    $newOrderShipping = [
                        'uuid' => Str::uuid()->toString(),
                        'order_shipping_temp_uuid' => $shipping->uuid,
                        'order_header_uuid' => $orderHeader->uuid,
                        'courier_uuid' => $shipping->courier_uuid,
                        'shipping_charge' => $orderHeader->total_shipping_charge,
                        'discount_shipping_charge' => $shipping->discount_shipping_charge,
                        'member_shipping_address_uuid' => $shipping->member_shipping_address_uuid,
                        'province' => $shipping->province,
                        'city' => $shipping->city,
                        'district' => $shipping->district,
                        'village' => $shipping->village,
                        'details' => $shipping->details,
                        'notes' => $shipping->notes,
                        'created_by' => $user,
                    ];

                    // Insert into order_shipping
                    $orderShipping = new OrderShipping($newOrderShipping);
                    $orderShipping->save();

                    $newOrderShippings[] = $newOrderShipping;
                }

                // Insert into order_statuses
                $newOrderStatus = [
                    'uuid' => Str::uuid()->toString(),
                    'order_header_uuid' => $orderHeader->uuid,
                    'status' => 1,
                    'reference_uuid' => $orderHeader->uuid,
                    'description' => 'Paid',
                    'remarks' => 'New transaction, complete payment',
                    // 'created_by' => $user->uuid,
                ];

                // Insert into order_statuses
                $newOrderStatusAdd = new OrderStatuses($newOrderStatus);
                $newOrderStatusAdd->save();

                $newOrderStatuses[] = $newOrderStatus;
            }

            DB::commit();
            return $this->core->setResponse(
                'success',
                'Order(s) approved.',
                $uuids,
                false,
                200
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->core->setResponse(
                'error',
                $th->getMessage(),
                [],
                FALSE,
                400
            );
        }
    }




    // ==========================================================================================


    public function calculateSCAmount($transaction)
    {
        // $transaction = transaksi::where('code_trans', $code_trans)->with(['transaksi_detail', 'province', 'user'])->first();
        $point_reward = $transaction->voucher_amount ? $transaction->voucher_amount : 0;

        $SH1 = 0;
        $SPV = 0;
        $SBV = 0;
        $Cashback = 0;

        foreach ($transaction->details as $transactiond) {
            $product = $transactiond; //barang::where('id', $transactiond->id_barang_fk)->first();

            if ($product->bv) {
                $percentPV = $product->pv / $product->price;
                $percentBV = $product->bv / $product->price;
                $TH1 = $transactiond->qty * $product->price;

                $sisaTH1 = $point_reward >= $TH1 ? 0 : $TH1 - $point_reward;
                $point_reward -= $TH1;

                if ($sisaTH1) {
                    $SH1 += $sisaTH1;
                    $SPV += $sisaTH1 * $percentPV;

                    $BV = $sisaTH1 * $percentBV;
                    $BV -= $BV * 0.05;
                    $SBV += $BV;

                    $Cashback += $sisaTH1 * ($product->cashback / 100);
                }
            }
        }

        return ['SH1' => $SH1, 'SPV' => $SPV, 'SBV' => $SBV, 'Cashback' => $Cashback];
    }

    public function confirmPaymentValid($uuid, $date, $memberUuid)
    {
        $money = new Money;
        $sms = new \App\AlvaMarvello\SMS;
        $joyhelper = new \App\AlvaMarvello\JoyHelper;

        // $userlogin = Auth()->user();
        // $transaction = transaksi::where('code_trans', $code)->whereIn('status', ['P', 'WP', 'CP', 'COD'])->with(['transaksi_detail', 'province', 'user'])->first();





        if (Auth::check()) {
            $user = Auth::user();
            $userUuid = $user->uuid;
        } else {
            return $this->core->setResponse(
                'error',
                'Invalid transaction for this user.',
                null,
                false,
                422
            );
        }


        $transaction = OrderHeader::where('uuid', $uuid)
            ->with(['member.effectiveRank', 'details.productPrice.product'])
            ->first();





        /*joybiz v1
    //paket Registrasi SC yang mengandung biaya reg dan selisih
    $scRegisterSpecialCase = array('RSC01','RSC02');
    $scRegisterSpecialCase2 = array('RSC04');
    */

        if ($transaction) {

            $user = !$transaction->member_uuid ?? Member::where('uuid', $transaction->member_uuid)->with(['sponsor', 'effective_rank'])->first();
            $membership = Member::where('uuid', $transaction->member_uuid)->first();

            $srank = isset($user->srank) ? $user->srank : null;

            $indent = false;
            $totalHargaWIB = 0;
            $selisihRetail = 0;
            $hasRegister = false;
            $qudu = 0;
            $quduBVG = 0;
            $hargaAsliCod = 0;



            // #jika transaksi sc terdapat paket register joybizer makan transaksi tersebut di rubah menjadi transaksi joybizer
            // if ($transaction->id_sc_fk && $hasRegister) {
            //     $transaction->id_cust_fk = $transaction->id_sc_fk;
            //     $transaction->id_sc_fk = null;
            // }

            // $transaction->transaction_date = $date;
            // if ($transaction->status == 'COD') {
            //     $transaction->status = 'S';
            // } else {
            //     $transaction->status = $indent ? 'I' : 'PC';
            // }
            // $transaction->approved_by = $userlogin ? $userlogin->id : null;
            // $saved = $transaction->save();

            // if ($transaction->pv_total >= 100) {
            //     $main_user = User::where('uid', $user->owner)->with('memberships')->first();
            //     if ($main_user) {
            //         // $main_user->dormant = null;
            //         // $main_user->save();

            //         // foreach ($main_user->memberships as $membership) {
            //         //     $membership->dormant = null;
            //         //     $membership->save();
            //         // }

            //         $dormant = \App\Dormant::where('owner', $main_user->uid)->first();
            //         $dormant->will = Carbon::parse($transaction->transaction_date)->addMonths(6)->toDateString();
            //         $dormant->save();
            //     }
            // }


            if (
                $membership->status != 1
                && $transaction->bv_total >= $membership->min_bv
            ) {
                $membership->activated_at =
                    $membership->status == 1
                    && $transaction->pv_total >= $membership->min_bv
                    ? $membership->activated_at
                    : $transaction->created_at;
                $membership->status = 1;
                $membership->save();
            }

            //jika transaksi Special Customer
            if ($transaction->id_sc_fk) {
                $SCtransaction = $this->calculateSCAmount($transaction);

                if ($transaction->pv_total > 0) {

                    if ($user->flag == 3 || $hasRegister) {

                        //if Customer buy with retail once change to SC
                        $user->flag = 2;
                        $user->status = 1;
                        $user->activated_at = $date;
                        $user->save();

                        #jika customer tidak upgrade membership
                        if ($user->flag == 2) {
                            $message_user = "Selamat! anda telah menjadi Special Customer kami. Login dengan " . $user->email . " & password yg didaftarkan di www.joybiz.co.id/";
                            $message_sponsor = "Selamat! Special Customer " . $user->username . " yang anda Sponsori telah aktif.";

                            $destination_user = $user->handphone;
                            $result_user = $sms->send($destination_user, $message_user);

                            // $destination_sponsor = $user->sponsor->handphone;
                            $destination_sponsor = $membership->sponsor->user->handphone;
                            $result_sponsor = $sms->send($destination_sponsor, $message_sponsor);
                        }
                    }

                    //$RealBV = ($transaction->bv_total * 0.875);				
                    //$Cashback = $totalHargaWIB * 0.083;

                    $RealBV = $SCtransaction['SBV'];
                    $RealPV = $SCtransaction['SPV'];
                    $Cashback = $SCtransaction['Cashback'];

                    //$Cashback = $SH1 * 0.083;

                    //if ($Cashback && !$voucher_amount){
                    if ($Cashback) {
                        $owner = Membership::where('jbid', $transaction->id_sc_fk)->first();
                        $note = "Cashback from your transaction " . $transaction->code_trans;
                        $TransferCashback = $money->topupVoucher($owner->uid, $Cashback, $note, $userlogin);
                    }

                    $selisihRetail -= 20000;
                    $selisihRetail = $selisihRetail > 0 ? $selisihRetail : 0;
                    if ($selisihRetail) {
                        $owner = Membership::where('jbid', $transaction->id_cust_fk)->first();
                        $note = "Cashback from Special Customer Registration " . $transaction->code_trans;
                        $TransferCashback = $money->topupVoucher($owner->uid, $selisihRetail, $note, $userlogin);
                    }
                }
            }


            //abodemen
            $transaction = new Transaction;
            $result = $transaction->generateAbodemenChild($transaction->code_trans);

            #$userCoupon = User::where('id',$transaction->id_cust_fk)->first();
            #$RewardCoupon = $this->monthyRewardCoupon($userCoupon);

            if ($transaction->is_pickup) {
                $transaction->pickup_code = encrypt(rand(111111, 999999));
                $transaction->save();
            }


            if ($saved) {

                #cashback
                foreach ($transaction['transaksi_detail'] as $transactiond) {
                    $product = barang::where('id', $transactiond->id_barang_fk)->first();
                    if ($product->cashback_gamma > 0 && $srank && $srank->srank >= 3) {
                        $cashback_amount = $product->cashback_gamma * $transactiond->qty;
                        $description = "Cashback " . $product->nama . " dari Transaksi " . $transaction->code_trans;

                        //money($type,$user, $amount, $credit, $description,$freeze=false,$transaction_code = null)
                        $money->money("cashback", $membership, $cashback_amount, false, $description, false, $transaction->code_trans);
                    }
                }
                #cashback

                $jbid = $transaction->id_cust_fk;

                $ppv = $transaction->pv_total;
                $pbv = $transaction->bv_total;
                $prv = $transaction->rv_total;

                $gpv = 0;
                $gbv = 0;
                $grv = 0;

                $jbp = 0;
                $bj = 0;
                $vj = 0;

                #$ppvj = $transaction->pv_plan_joy;
                $ppvj = $transaction->pv_plan_biz; #$transaction->pv_total;
                #$pbvj = $transaction->bv_plan_joy;
                $pbvj = $transaction->bv_plan_biz; #$transaction->bv_total;
                $prvj = $transaction->rv_plan_biz; #$transaction->bv_total;
                $gpvj = 0;
                $gbvj = 0;
                $grvj = 0;

                $ppvb = 0; #$transaction->pv_plan_biz;
                $pbvb = 0; #$transaction->bv_plan_biz;
                $gpvb = 0;
                $gbvb = 0;

                $omzet = $transaction->purchase_cost;
                $omzet_joy = $transaction->price_joy;
                $omzet_biz = $transaction->price_biz;
                $omzet_with_bv = $transaction->omzet_with_bv ? ($transaction->omzet_with_bv / 20000) : ($omzet_joy + $omzet_biz) / 20000;

                if ($pbv) $this->update_Effective_Rank_Sim($jbid, $ppv, $pbv, $gpv, $gbv, $date, $jbp, $bj, $vj, $ppvj, $pbvj, $gpvj, $gbvj, $ppvb, $pbvb, $gpvb, $gbvb, $omzet, $omzet_joy, $omzet_biz, $omzet_with_bv, $qudu, $quduBVG, $prvj, $grvj, $prv, $grv);

                #$reset = $joyhelper->clearJoyData($now);
                $result = $joyhelper->syncJoyDatabyCode($code);
            }

            $status = true;
            $message = "Transaction " . $transaction->code_trans . " settled with success!!";
        } else {

            $status = false;
            $message = "Transaction not found!!";
        }

        $result = ['status' => $status, 'message' => $message];
        return $result;
    }


    // =============================================================================================================================




    //Get list of orders
    public function getOrderList($request)
    {
        DB::enableQueryLog();

        // Get order list
        $orders = OrderHeader::query();

        if ($request->input('start') && $request->input('end')) {
            $start = $request->input('start');
            $end = $request->input('end');

            $orders = $orders->whereBetween(DB::raw('created_at::date'), [$start, $end]);
        }

        if ($request->input('status') !== null) {
            $status = $request->input('status');

            $orders = $orders->where('status', $status);
        }

        if ($request->input('member_uuid') !== null) {
            $member_uuid = $request->input('member_uuid');

            $orders = $orders->where('member_uuid', $member_uuid);
        }

        $orders = $orders->orderBy('created_at', 'asc')->get();

        if (!$orders) {
            return $this->core->setResponse(
                'error',
                'Order not exist.',
                NULL,
                FALSE,
                400
            );
        }

        return $this->core->setResponse(
            'success',
            'Order list.',
            $orders,
        );
    }

    //Get Order by uuids
    public function getOrderDetails($uuid)
    {
        if (!Str::isUuid($uuid)) {
            return $this->core->setResponse(
                'error',
                'Invalid UUID format',
                NULL,
                FALSE,
                400
            );
        }

        $order = OrderHeader::where([
            'uuid' => $uuid,
        ])
            ->with('details', 'payments', 'shipping')
            ->get();

        if (!isset($order)) {
            return $this->core->setResponse(
                'error',
                'Order Not Found',
                NULL,
                FALSE,
                400
            );
        }

        return $this->core->setResponse(
            'success',
            'Order Found',
            $order
        );
    }

    // Delete Orders
    public function destroyBulk($request)
    {

        $validator = $this->validation(
            'delete',
            $request
        );

        if ($validator->fails()) {
            return $this->core->setResponse(
                'error',
                $validator->messages()->first(),
                NULL,
                false,
                422
            );
        }

        $uuids = $request->input('uuids');
        $orders = null;
        try {
            DB::beginTransaction();
            DB::enableQueryLog();

            $orders = OrderHeader::lockForUpdate()
                ->whereIn(
                    'uuid',
                    $uuids
                );

            // Compare the count of found UUIDs with the count from the request array
            if (
                !$orders ||
                (count($orders->get()) !== count($uuids))
            ) {
                return response()->json(
                    ['message' => 'Orders fail to deleted, because invalid uuid(s)'],
                    400
                );
            }

            // Check Auth & update user uuid to deleted_by
            $user = null;
            if (Auth::check()) {
                $auth = Auth::user();
                $user = $auth->uuid;
                $orders->update([
                    'deleted_by' => $user
                ]);
            }

            $orders->delete();

            $orderDetails = OrderDetail::lockForUpdate()->whereIn('order_header_uuid', $uuids);
            $orderDetails->update([
                'deleted_by' => $user
            ]);
            $orderDetails->delete();

            $orderPayments = OrderPayment::lockForUpdate()->whereIn('order_header_uuid', $uuids);
            $orderPayments->update([
                'deleted_by' => $user
            ]);
            $orderPayments->delete();

            $orderShipping = OrderShipping::lockForUpdate()->whereIn('order_header_uuid', $uuids);
            $orderShipping->update([
                'deleted_by' => $user
            ]);
            $orderShipping->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                ['message' => 'Error during bulk deletion ' . $e->getMessage()],
                500
            );
        }

        return $this->core->setResponse(
            'success',
            "Orders deleted",
            null,
            200
        );
    }

    private function validation($type = null, $request)
    {
        switch ($type) {

            case 'delete':

                $validator = [
                    'uuids' => 'required|array',
                    'uuids.*' => 'required|uuid',
                    // 'uuids.*' => 'required|exists:warehouses,uuid',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
