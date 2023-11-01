<?php

namespace App\Services\Orders;

use App\Helpers\Bonuses\VoucherHelper;
use app\Libraries\Core;
use App\Models\Orders\OrderStatuses;
use App\Models\Orders\Temporary\OrderDetailTemp;
use App\Models\Orders\Temporary\OrderGroupHeaderTemp;
use App\Models\Orders\Temporary\OrderGroupPaymentTemp;
use App\Models\Orders\Temporary\OrderHeaderTemp;
use App\Models\Orders\Temporary\OrderPaymentTemp;
use App\Models\Orders\Temporary\OrderShippingTemp;
use Carbon\Carbon;
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

      // Check Auth & update user uuid 
      $userlogin = null;
      if (Auth::check()) {
        $user = Auth::user();
        $userlogin = $user->uuid;
      }

      $orderHeaders = $request->all();

      $groupUuid = Str::uuid();
      $groupOrderSeq = 0;
      $groupTotalDiscountValue = 0;
      $groupTotalDiscountValueAmount = 0;
      $groupTotalVoucherAmount = 0;
      $groupTotalAmount = 0;
      $groupTotalAmountAfterDiscount = 0;
      $groupTotalCashback = 0;
      $groupTotalCashbackReseller = 0;
      $groupTotalShippingCharge = 0;
      $groupTotalShippingDiscount = 0;
      $groupTotalShippingNett = 0;
      $groupTotalPaymentCharge = 0;
      $groupTaxAmount = 0;
      $groupTotalCharge = 0;
      $groupTotalAmountSummary = 0;
      $groupTotalPv = 0;
      $groupTotalXv = 0;
      $groupTotalBv = 0;
      $groupTotalRv = 0;
      $groupTotalPv = 0;
      $groupTotalOrderToShipped = 0;
      $groupTotalOrderToPickedUp = 0;
      $totalCharge = 0;
      $totalAmountSummary = 0;

      $newOrder = collect();

      foreach ($orderHeaders as $orderHeader) {

        $totalCharge = ($orderHeader['total_shipping_charge'] - $orderHeader['total_shipping_discount']) + $orderHeader['total_payment_charge'];
        $totalAmountSummary = $orderHeader['total_amount'] - ($orderHeader['total_discount_value_amount'] + $orderHeader['total_voucher_amount'] + $orderHeader['total_shipping_discount']);

        $groupOrderSeq += 1;
        $groupTotalDiscountValue += $orderHeader['total_discount_value'];
        $groupTotalDiscountValueAmount += $orderHeader['total_discount_value_amount'];
        $groupTotalVoucherAmount += $orderHeader['total_voucher_amount'];
        $groupTotalAmount += $orderHeader['total_amount'];
        $groupTotalAmountAfterDiscount += $orderHeader['total_amount_after_discount'];
        $groupTotalCashback += $orderHeader['total_cashback'];
        $groupTotalCashbackReseller += $orderHeader['total_cashback_reseller'];
        $groupTotalShippingCharge += $orderHeader['total_shipping_charge'];
        $groupTotalShippingDiscount += $orderHeader['total_shipping_discount'];
        $groupTotalShippingNett += $orderHeader['total_shipping_nett'];
        $groupTotalPaymentCharge += $orderHeader['total_payment_charge'];
        $groupTaxAmount += $orderHeader['tax_amount'];
        $groupTotalCharge += $totalCharge;
        $groupTotalAmountSummary += $totalAmountSummary;
        $groupTotalPv += $orderHeader['total_pv'];
        $groupTotalXv += $orderHeader['total_xv'];
        $groupTotalBv += $orderHeader['total_bv'];
        $groupTotalRv += $orderHeader['total_rv'];
        $groupTotalOrderToShipped += $orderHeader['ship_type'] == '1' ? $orderHeader['ship_type'] : 0;
        $groupTotalOrderToPickedUp += 0;

        //if count($orderHeaders) == $groupTransactionSeq then inser group header
        if (count($orderHeaders) == $groupOrderSeq) {
          $newGroupHeader = [
            'uuid' => $groupUuid,
            'member_uuid' => $orderHeader['member_uuid'],
            'total_discount_value' => $groupTotalDiscountValue,
            'total_discount_value_amount' => $groupTotalDiscountValueAmount,
            'total_voucher_amount' => $groupTotalVoucherAmount,
            'total_amount' => $groupTotalAmount,
            'total_amount_after_discount' => $groupTotalAmountAfterDiscount,
            'total_cashback' => $groupTotalCashback,
            'total_cashback_reseller' => $groupTotalCashbackReseller,
            'total_shipping_charge' => $groupTotalShippingCharge,
            'total_shipping_discount' => $groupTotalShippingDiscount,
            'total_shipping_nett' => $groupTotalShippingNett,
            'total_payment_charge' => $groupTotalPaymentCharge,
            'tax_amount' => $groupTaxAmount,
            'total_charge' => $groupTotalCharge,
            'total_amount_summary' => $groupTotalAmountSummary,
            'total_pv' => $groupTotalPv,
            'total_xv' => $groupTotalXv,
            'total_bv' => $groupTotalBv,
            'total_rv' => $groupTotalRv,
            'total_order_to_shipped' => $groupTotalOrderToShipped,
            'total_order_to_picked_up' => $groupTotalOrderToPickedUp,
            'status' => "0",
            'transaction_date' => Carbon::now(),
            'created_by' => $userlogin ? $userlogin : $orderHeader['member_uuid'],
          ];

          // Insert into order_group_headers_temp
          $newGroupHeaderAdd = new OrderGroupHeaderTemp($newGroupHeader);
          $newGroupHeaderAdd->save();
          $newGroupHeaderTemp = $newGroupHeaderAdd->where('uuid', $groupUuid)->first();
        }

        // New Order Header;
        $newOrderHeader = [
          'uuid' => Str::uuid(),
          'order_group_header_temp_uuid' => $groupUuid,
          'member_uuid' => $orderHeader['member_uuid'],
          'price_code_uuid' => $orderHeader['price_code_uuid'],
          'remarks' => $orderHeader['remarks'],
          // 'total_discount_value' => $orderHeader['total_discount_value'],
          'total_discount_value_amount' => $orderHeader['total_discount_value_amount'],
          'total_voucher_amount' => $orderHeader['total_voucher_amount'],
          'total_amount' => $orderHeader['total_amount'],
          'total_amount_after_discount' => $orderHeader['total_amount'] - $orderHeader['total_discount_value_amount'],
          'total_cashback' => $orderHeader['total_cashback'],
          'total_cashback_reseller' => $orderHeader['total_cashback_reseller'],
          'total_shipping_charge' => $orderHeader['total_shipping_charge'],
          'total_shipping_discount' => $orderHeader['total_shipping_discount'],
          'total_shipping_nett' => $orderHeader['total_shipping_charge'] - $orderHeader['total_shipping_discount'],
          'total_payment_charge' => $orderHeader['total_payment_charge'],
          'tax_amount' => $orderHeader['tax_amount'],
          'total_charge' => $totalCharge,
          // ($orderHeader['total_shipping_charge'] - $orderHeader['total_shipping_discount']) + $orderHeader['total_payment_charge'],
          'total_amount_summary' => $totalAmountSummary,
          // $orderHeader['total_amount'] - ($orderHeader['total_discount_value_amount'] + $orderHeader['total_voucher_amount'] + $orderHeader['total_shipping_discount']),
          'total_pv' => $orderHeader['total_pv'],
          'total_xv' => $orderHeader['total_xv'],
          'total_bv' => $orderHeader['total_bv'],
          'total_rv' => $orderHeader['total_rv'],
          'ship_type' => $orderHeader['ship_type'],
          'status' => "0",
          'airway_bill_no' => $orderHeader['airway_bill_no'],
          'transaction_date' => Carbon::now(),
          'created_by' => $userlogin ? $userlogin : $orderHeader['member_uuid'],
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
            'uuid' => Str::uuid(),
            'order_group_header_temp_uuid' => $groupUuid,
            'order_header_temp_uuid' => $newOrderHeaderAdd['uuid'],
            'product_uuid' => $orderDetail['product_uuid'],
            'product_attribute_uuid' => $orderDetail['product_attribute_uuid'] ? $orderDetail['product_attribute_uuid'] : null,
            'product_price_uuid' => $orderDetail['product_price_uuid'],
            'is_product_group' => $orderDetail['is_product_group'],
            'qty' => $orderDetail['qty'],
            'price' => $orderDetail['price'],
            'discount_type' => $orderDetail['discount_type'],
            'discount_value' => $orderDetail['discount_value'],
            'discount_value_amount' => $orderDetail['discount_value_amount'],
            'price_after_discount' => $orderDetail['price_after_discount'],
            'cashback' => $orderDetail['cashback'],
            'cashback_reseller' => $orderDetail['cashback_reseller'],
            'pv' => $orderDetail['pv'],
            'xv' => $orderDetail['xv'],
            'bv' => $orderDetail['bv'],
            'rv' => $orderDetail['rv'],
            'status' => $orderDetail['status'],
            'created_by' => $userlogin ? $userlogin : $orderHeader['member_uuid'],
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
          // Start check if payment include voucher cash/product
          $voucherHelper = new VoucherHelper;
          if (
            $orderPayment['payment_type_uuid'] == '92851148-5dd6-4360-a644-7948e9aaf54e' || // Voucher Cash
            $orderPayment['payment_type_uuid'] == '1ce39ab0-068c-4d2e-89c3-5d21eeae6175' // Voucher Product
          ) {
            $voucherHelper->use($orderHeader['member_uuid'], $orderPayment['amount'], $groupUuid);
          }
          // End check if payment include voucher cash/product

          $newOrderPayment = [
            'uuid' => Str::uuid(),
            'order_group_header_temp_uuid' => $groupUuid,
            'order_header_temp_uuid' => $newOrderHeaderAdd['uuid'],
            'payment_type_uuid' => $orderPayment['payment_type_uuid'],
            'voucher_uuid' => $orderPayment['voucher_uuid'] ? $orderPayment['voucher_uuid'] : null,
            'voucher_code' => $orderPayment['voucher_code'] ? $orderPayment['voucher_code'] : null,
            'amount' => $orderPayment['amount'],
            // 'total_discount' => $orderPayment['total_discount'],
            // 'total_amount_after_discount' => $orderPayment['total_amount_after_discount'],
            'remarks' => $orderPayment['remarks'],
            'created_by' => $userlogin ? $userlogin : $orderHeader['member_uuid'],
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

        // foreach ($orderShipping as $shippingInfo) {
        $orderShipping = collect($orderShipping);

        $newOrderShipping = [
          'uuid' => Str::uuid(),
          'order_group_header_temp_uuid' => $groupUuid,
          'order_header_temp_uuid' => $newOrderHeaderAdd['uuid'],
          'courier_uuid' => $orderShipping['courier_uuid'],
          'member_shipping_address_uuid' => $orderShipping['address_uuid'],
          'shipping_charge' => $orderHeader['total_shipping_charge'],
          'discount_shipping_charge' =>
          $orderHeader['total_shipping_discount']
            ? $orderHeader['total_shipping_discount']
            : 0,
          'province' => $orderShipping['province'],
          'city' => $orderShipping['city'],
          'district' => $orderShipping['district'],
          'village' => $orderShipping['village'],
          'details' => $orderShipping['details'],
          'notes' => $orderShipping['notes'],
          'created_by' => $userlogin ? $userlogin : $orderHeader['member_uuid'],
        ];

        if ($orderShipping->has('address_uuid')) {
          $newOrderShipping['member_shipping_address_uuid'] = $orderShipping['address_uuid'];
        }

        if ($orderShipping->has('remarks')) {
          $newOrderShipping['remarks'] = $orderShipping['remarks'];
        }

        // Insert into order_shipping_temp
        $newOrderShippingAdd = new OrderShippingTemp($newOrderShipping);
        $newOrderShippingAdd->save();

        $newOrderShippings[] = $newOrderShipping;
        // }

        // Insert into order_statuses
        $newOrderStatus = [
          'uuid' => Str::uuid(),
          'order_header_uuid' => $newOrderHeaderAdd['uuid'],
          'status' => "0",
          'reference_uuid' => $newOrderHeaderAdd['uuid'],
          'description' => 'Pending',
          'remarks' => 'New transaction, incomplete payment',
          'created_by' => $userlogin ? $userlogin : $orderHeader['member_uuid'],
        ];

        // Insert into order_statuses
        $newOrderStatusAdd = new OrderStatuses($newOrderStatus);
        $newOrderStatusAdd->save();

        $newOrderStatuses[] = $newOrderStatus;

        //Result
        $newHeader = OrderHeaderTemp::where('uuid', array_column($newOrderHeaders, 'uuid'))->get();

        $newDetails = OrderDetailTemp::where('order_header_temp_uuid', array_column($newOrderHeaders, 'uuid'))->get();

        $newPayments = OrderPaymentTemp::where('order_header_temp_uuid', array_column($newOrderHeaders, 'uuid'))->get();

        $newShippings = OrderShippingTemp::where('order_header_temp_uuid', array_column($newOrderHeaders, 'uuid'))->get();

        $newStatuses = OrderStatuses::where('order_header_uuid', array_column($newOrderHeaders, 'uuid'))->get();

        $newOrder->push([
          "header" => $newHeader,
          "detail" => $newDetails,
          "payment" => $newPayments,
          "shipping" => $newShippings,
          "status" => $newStatuses,
        ]);
      }

      // $newStatuses = OrderGroupHeaderTemp::where('order_header_uuid', array_column($newOrderHeaders, 'uuid'))->get();
      $newOrder["group_header"] = $newGroupHeaderTemp;

      $groupPayments = OrderPaymentTemp::select(
        'order_group_header_temp_uuid',
        'payment_type_uuid',
        DB::raw('SUM(amount) as amount'),
      )
        ->where('order_group_header_temp_uuid', $groupUuid)
        ->groupBy('order_group_header_temp_uuid', 'payment_type_uuid')
        ->get();

      $groupPaymentAdd = $groupPayments->map(function ($groupPayment) use ($userlogin, $orderHeader) {
        $groupPayment->uuid = Str::uuid()->toString();
        $groupPayment->created_by = $userlogin ? $userlogin : $orderHeader['member_uuid'];
        $groupPayment->updated_by = $userlogin ? $userlogin : $orderHeader['member_uuid'];
        $groupPayment->created_at = Carbon::now();
        $groupPayment->updated_at = Carbon::now();;
        return $groupPayment;
      });

      $newGroupPayments = new OrderGroupPaymentTemp();
      $newGroupPayments->insert($groupPaymentAdd->toArray());

      $newOrder["group_header_payments"] = $newGroupPayments->where('order_group_header_temp_uuid', $groupUuid)->get();

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
  public function getGroupOrderList($request)
  {
    DB::enableQueryLog();

    // Get order list
    $groupOrders = OrderGroupHeaderTemp::query();

    if ($request->input('start') && $request->input('end')) {
      $start = $request->input('start');
      $end = $request->input('end');

      $groupOrders = $groupOrders->whereBetween(DB::raw('created_at::date'), [$start, $end]);
    }

    if ($request->input('status') !== null) {
      $status = $request->input('status');

      $groupOrders = $groupOrders->where('status', $status);
    }

    if ($request->input('member_uuid') !== null) {
      $member_uuid = $request->input('member_uuid');

      $groupOrders = $groupOrders->where('member_uuid', $member_uuid);
    }


    $groupOrders = $groupOrders->with('payments', 'headers.member', 'headers.details', 'headers.payments', 'headers.shipping')->orderBy('created_at', 'asc')->get();

    if (!$groupOrders) {
      return $this->core->setResponse(
        'error',
        'Group Order not exist.',
        NULL,
        FALSE,
        400
      );
    }

    return $this->core->setResponse(
      'success',
      'Group Order list.',
      $groupOrders,
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

      $orders = OrderHeaderTemp::lockForUpdate()
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

      $orderDetails = OrderDetailTemp::lockForUpdate()->whereIn('order_header_temp_uuid', $uuids);
      $orderDetails->update([
        'deleted_by' => $user
      ]);
      $orderDetails->delete();

      $orderPayments = OrderPaymentTemp::lockForUpdate()->whereIn('order_header_temp_uuid', $uuids);
      $orderPayments->update([
        'deleted_by' => $user
      ]);
      $orderPayments->delete();

      $orderShipping = OrderShippingTemp::lockForUpdate()->whereIn('order_header_temp_uuid', $uuids);
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
          // 'uuids.*' => 'required|exists:product_products,uuid',
        ];

        break;

      case 'create' || 'update':

        $validator = [
          '*.price_code_uuid' => 'required|string|max:255|min:2',
          '*.member_uuid' => 'required|max:140|min:5',
          '*.description' => 'string|max:150',
          '*.remarks' => 'string|max:150',
          // '*.total_discount_value' => 'required|numeric',
          '*.total_discount_value_amount' => 'required|numeric',
          // '*.total_amount_after_discount' => 'required|numeric',
          '*.total_voucher_amount' => 'numeric',
          '*.total_cashback' => 'numeric',
          '*.total_cashback_reseller' => 'numeric',
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
          '*.products.*.cashback' => 'numeric',
          '*.products.*.cashback_reseller' => 'numeric',
          '*.products.*.pv' => 'required|numeric',
          '*.products.*.xv' => 'required|numeric',
          '*.products.*.bv' => 'required|numeric',
          '*.products.*.rv' => 'required|numeric',
          '*.products.*.status' => 'required|in:0,1,2,3,4,5,6',

          '*.payments.*.payment_type_uuid' => 'required|uuid',
          '*.payments.*.voucher_uuid' => 'string',
          '*.payments.*.voucher_code' => 'string',
          '*.payments.*.amount' => 'required|numeric|min:1',
          // '*.payments.*.total_discount' => 'required|numeric',
          // '*.payments.*.total_amount_after_discount' => 'required|numeric',
          '*.payments.*.remarks' => 'string',

          '*.shipping_info.courier_uuid' => 'required|uuid',
          '*.shipping_info.remarks' => 'string',
          // '*.shipping_info.*.shipping_charge' => 'required|numeric',
          // '*.shipping_info.*.discount_shipping_charge' => 'required|numeric',


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
