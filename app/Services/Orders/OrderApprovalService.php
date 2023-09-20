<?php

namespace App\Services\Orders;

use app\Libraries\Core;
use App\Models\Orders\OrderStatuses;
use App\Models\Orders\Production\OrderDetail;
use App\Models\Orders\Production\OrderHeader;
use App\Models\Orders\Production\OrderPayment;
use App\Models\Orders\Production\OrderShipping;
use App\Models\Orders\Temporary\OrderHeaderTemp;
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
                $user = Auth::user();
            }

            $orderTemp = OrderHeaderTemp::with('details', 'payments', 'shipping')
                ->whereIn('uuid', $uuids)
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

            // print_r($orders->get());

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
