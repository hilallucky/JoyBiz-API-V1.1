<?php

namespace App\Services\Orders;

use app\Libraries\Core;
use App\Models\Orders\OrderStatuses;
use App\Models\Orders\Temporary\OrderDetailTemp;
use App\Models\Orders\Temporary\OrderHeaderTemp;
use App\Models\Orders\Temporary\OrderPaymentTemp;
use App\Models\Orders\Temporary\OrderShippingTemp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    public function store($request)
    {
        $validator = $this->validation(
            'create',
            $request
        );

        if ($validator->fails()) {
            return $this->core->setResponse(
                'error',
                $validator->messages()->first(),
                [],
                false,
                422
            );
        }

        try {
            DB::beginTransaction();
            DB::enableQueryLog();

            // Check Auth & update user uuid to deleted_by
            if (Auth::check()) {
                $user = Auth::user();
            }

            $orderHeaders = $request->all();

            foreach ($orderHeaders as $orderHeader) {
                // New Order Header;
                $newOrderHeader = [
                    'uuid' => Str::uuid()->toString(),
                    'price_code_uuid' => $orderHeader['price_code_uuid'],
                    'member_uuid' => $orderHeader['member_uuid'],
                    'remarks' => $orderHeader['remarks'],
                    'total_discount_value' => $orderHeader['total_discount_value'],
                    'total_discount_value_amount' => $orderHeader['total_discount_value_amount'],
                    'total_price_after_discount' => $orderHeader['total_price_after_discount'],
                    'total_amount' => $orderHeader['total_amount'],
                    'total_shipping_charge' => $orderHeader['total_shipping_charge'],
                    'total_payment_charge' => $orderHeader['total_payment_charge'],
                    'total_amount_summary' => $orderHeader['total_amount_summary'],
                    'total_pv' => $orderHeader['total_pv'],
                    'total_xv' => $orderHeader['total_xv'],
                    'total_bv' => $orderHeader['total_bv'],
                    'total_rv' => $orderHeader['total_rv'],
                    'status' => "0",
                    'airway_bill_no' => $orderHeader['airway_bill_no'],
                    // 'created_by' => $user->uuid,
                ];

                // Insert into order_headers_temp
                $newOrderHeaderAdd = new OrderHeaderTemp($newOrderHeader);
                $newOrderHeaderAdd->save();
                $newOrderHeaders[] = $newOrderHeader;

                // New Order Details
                $orderDetails = $orderHeader['products'];

                if (
                    $orderDetails === 0 ||
                    empty($orderDetails)
                ) {
                    DB::rollback();
                    return $this->core->setResponse(
                        'error',
                        'Product cannot be empty.',
                        [],
                        FALSE,
                        400
                    );
                }

                foreach ($orderDetails as $orderDetail) {
                    $newOrderDetail = [
                        'uuid' => Str::uuid()->toString(),
                        'order_header_temp_uuid' => $newOrderHeaderAdd['uuid'],
                        'product_price_uuid' => $orderDetail['product_price_uuid'],
                        'qty' => $orderDetail['qty'],
                        'price' => $orderDetail['price'],
                        'discount_type' => $orderDetail['discount_type'],
                        'discount_value' => $orderDetail['discount_value'],
                        'discount_value_amount' => $orderDetail['discount_value_amount'],
                        'price_after_discount' => $orderDetail['price_after_discount'],
                        'pv' => $orderDetail['pv'],
                        'xv' => $orderDetail['xv'],
                        'bv' => $orderDetail['bv'],
                        'rv' => $orderDetail['rv'],
                        // 'created_by' => $user->uuid,
                    ];

                    // Insert into order_details_temp
                    $newOrderDetailAdd = new OrderDetailTemp($newOrderDetail);
                    $newOrderDetailAdd->save();
                    $newOrderDetails[] = $newOrderDetail;
                }

                // New Order Payments
                $orderPayments = $orderHeader['payments'];

                if (
                    $orderPayments === 0 ||
                    empty($orderPayments)
                ) {
                    DB::rollback();
                    return $this->core->setResponse(
                        'error',
                        'Payment cannot be empty.',
                        [],
                        FALSE,
                        400
                    );
                }

                foreach ($orderPayments as $orderPayment) {
                    $newOrderPayment = [
                        'uuid' => Str::uuid()->toString(),
                        'order_header_temp_uuid' => $newOrderHeaderAdd['uuid'],
                        'payment_type_uuid' => $orderPayment['payment_type_uuid'],
                        'total_amount' => $orderPayment['total_amount'],
                        'total_discount' => $orderPayment['total_discount'],
                        'total_amount_after_discount' => $orderPayment['total_amount_after_discount'],
                        'remarks' => $orderPayment['remarks'],
                        // 'created_by' => $user->uuid,
                    ];

                    // Insert into order_payments_temp
                    $newOrderPaymentAdd = new OrderPaymentTemp($newOrderPayment);
                    $newOrderPaymentAdd->save();

                    $newOrderPayments[] = $newOrderPayment;
                }

                // New Order Shipping
                $orderShipping = $orderHeader['shipping_info'];

                if (
                    $orderShipping === 0 ||
                    empty($orderShipping)
                ) {
                    DB::rollback();
                    return $this->core->setResponse(
                        'error',
                        'Shipping info cannot be empty.',
                        [],
                        FALSE,
                        400
                    );
                }

                foreach ($orderShipping as $shippingInfo) {
                    $newOrderShipping = [
                        'uuid' => Str::uuid()->toString(),
                        'order_header_temp_uuid' => $newOrderHeaderAdd['uuid'],
                        'courier_uuid' => $shippingInfo['courier_uuid'],
                        'shipping_charge' => $orderHeader['total_shipping_charge'],
                        'province' => $shippingInfo['province'],
                        'city' => $shippingInfo['city'],
                        'district' => $shippingInfo['district'],
                        'village' => $shippingInfo['village'],
                        'details' => $shippingInfo['details'],
                        'notes' => $shippingInfo['notes'],
                        'discount_shipping_charge' =>
                        $shippingInfo['discount_shipping_charge']
                            ? $shippingInfo['discount_shipping_charge']
                            : 0,
                        // 'created_by' => $user->uuid,
                    ];

                    if (isset($shippingInfo['address_uuid']) && $shippingInfo['address_uuid'] !== "") {
                        $newOrderShipping['member_address_uuid'] = $shippingInfo['address_uuid'];
                    }

                    // Insert into order_shipping_temp
                    $newOrderShippingAdd = new OrderShippingTemp($newOrderShipping);
                    $newOrderShippingAdd->save();

                    $newOrderShippings[] = $newOrderShipping;
                }

                // Insert into order_statuses
                $newOrderStatus = [
                    'uuid' => Str::uuid()->toString(),
                    'order_header_uuid' => $newOrderHeaderAdd['uuid'],
                    'status' => "0",
                    'reference_uuid' => $newOrderHeaderAdd['uuid'],
                    'description' => 'Pending',
                    'remarks' => 'New transaction, incomplete payment',
                    // 'created_by' => $user->uuid,
                ];

                // Insert into order_statuses
                $newOrderStatusAdd = new OrderStatuses($newOrderStatus);
                $newOrderStatusAdd->save();

                $newOrderStatuses[] = $newOrderStatus;
            }

            $newHeader = OrderHeaderTemp::whereIn('uuid', array_column($newOrderHeaders, 'uuid'))->get();

            $newDetails = OrderDetailTemp::whereIn('uuid', array_column($newOrderDetails, 'uuid'))->get();

            $newPayments = OrderPaymentTemp::whereIn('uuid', array_column($newOrderPayments, 'uuid'))->get();

            $newShippings = OrderShippingTemp::whereIn('uuid', array_column($newOrderShippings, 'uuid'))->get();

            $newStatuses = OrderStatuses::whereIn('uuid', array_column($newOrderStatuses, 'uuid'))->get();

            $newOrder = [
                "header" => $newHeader,
                "detail" => $newDetails,
                "payment" => $newPayments,
                "shipping" => $newShippings,
                "status" => $newStatuses,
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Order fail to created. == ' . $e->getMessage(),
                [],
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Order created',
            $newOrder,
            false,
            201
        );
    }

    //Get list of orders
    public function getOrderList($request)
    {
        DB::enableQueryLog();

        // Get order list
        $orders = OrderHeaderTemp::query();

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

        $order = OrderHeaderTemp::where([
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

    private function validation($type = null, $request)
    {

        switch ($type) {

            case 'delete':

                $validator = [
                    'uuids' => 'required|array',
                    'uuids.*' => 'required|uuid',
                    // 'uuids.*' => 'required|exists:product_products,uuid',
                ];

                break;

            case 'create' || 'update':

                $validator = [
                    '*.price_code_uuid' => 'required|string|max:255|min:2',
                    '*.member_uuid' => 'required|max:140|min:5',
                    '*.description' => 'string|max:150',
                    '*.remarks' => 'string|max:150',
                    '*.total_discount_value' => 'required|numeric',
                    '*.total_discount_value_amount' => 'required|numeric',
                    '*.total_price_after_discount' => 'required|numeric',
                    '*.total_amount' => 'required|numeric',
                    '*.total_shipping_charge' => 'required|numeric',
                    '*.total_payment_charge' => 'required|numeric',
                    '*.total_amount_summary' => 'required|numeric',
                    '*.total_pv' => 'required|numeric',
                    '*.total_xv' => 'required|numeric',
                    '*.total_bv' => 'required|numeric',
                    '*.total_rv' => 'required|numeric',
                    '*.airway_bill_no' => 'string|max:50',

                    '*.products.*.product_price_uuid' => 'required|uuid',
                    '*.products.*.qty' => 'required|numeric|min:1',
                    '*.products.*.price' => 'required|numeric',
                    '*.products.*.discount_type' => 'required|in:percentage,amount',
                    '*.products.*.discount_value' => 'required|numeric',
                    '*.products.*.discount_value_amount' => 'required|numeric',
                    '*.products.*.price_after_discount' => 'required|numeric',
                    '*.products.*.pv' => 'required|numeric',
                    '*.products.*.xv' => 'required|numeric',
                    '*.products.*.bv' => 'required|numeric',
                    '*.products.*.rv' => 'required|numeric',

                    '*.payments.*.payment_type_uuid' => 'required|uuid',
                    '*.payments.*.total_amount' => 'required|numeric|min:1',
                    '*.payments.*.total_discount' => 'required|numeric',
                    '*.payments.*.total_amount_after_discount' => 'required|numeric',
                    '*.payments.*.remarks' => 'string',

                    '*.shipping_info.*.courier_uuid' => 'required|uuid',
                    '*.shipping_info.*.shipping_charge' => 'required|numeric',
                    '*.shipping_info.*.discount_shipping_charge' => 'required|numeric',


                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make(
            $request->all(),
            $validator
        );
    }
}
