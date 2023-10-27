<?php

namespace App\Services\Orders;

use App\Helpers\Bonuses\Helper;
use app\Libraries\Core;
use App\Models\Bonuses\BonusRank;
use App\Models\Bonuses\BonusRankLog;
use App\Models\Bonuses\BonusWeekly;
use App\Models\Bonuses\Joys\JoyBonusSummary;
use App\Models\Bonuses\Joys\JoyData;
use App\Models\Bonuses\Joys\JoyPointReward;
use App\Models\Calculations\Bonuses\Period;
use App\Models\Members\Member;
use App\Models\Orders\OrderStatuses;
use App\Models\Orders\Production\OrderDetail;
use App\Models\Orders\Production\OrderGroupHeader;
use App\Models\Orders\Production\OrderGroupPayment;
use App\Models\Orders\Production\OrderHeader;
use App\Models\Orders\Production\OrderPayment;
use App\Models\Orders\Production\OrderShipping;
use App\Models\Orders\Temporary\OrderGroupHeaderTemp;
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
    return $this->approveOrderAction($request->uuids);
  }


  public function approveOrderAction($uuids)
  {
    try {

      DB::beginTransaction();
      DB::enableQueryLog();

      // Check Auth & update user uuid to deleted_by
      $user =  null;
      if (Auth::check()) {
        $auth = Auth::user();
        $user = $auth->uuid;
      }

      $groupOrderTemp = OrderGroupHeaderTemp::with('payments', 'headers.member', 'headers.details', 'headers.payments', 'headers.shipping')
        ->where([['uuid', $uuids], ['status', 0]])->orderBy('created_at', 'asc')->get();

      // Compare the count of found UUIDs with the count from the request array
      if (
        !$groupOrderTemp ||
        (count($groupOrderTemp) !== count($uuids))
      ) {
        return response()->json(
          ['message' => 'Orders fail to approve, because invalid uuid(s)'],
          400
        );
      }

      // New Group Order Header
      foreach ($groupOrderTemp as $groupOrder) {
        $groupOrderUuid = Str::uuid();
        $newGroupOrder = [
          'uuid' => $groupOrderUuid,
          'order_group_header_temp_uuid' => $groupOrder->uuid,
          'member_uuid' => $groupOrder->member_uuid,
          'remarks' => $groupOrder->remarks,
          'total_discount_value' => $groupOrder->total_discount_value,
          'total_discount_value_amount' => $groupOrder->total_discount_value_amount,
          'total_voucher_amount' => $groupOrder->total_voucher_amount,
          'total_amount' => $groupOrder->total_amount,
          'total_amount_after_discount' => $groupOrder->total_amount_after_discount,
          'cashback' => $groupOrder->cashback,
          'cashback_reseller' => $groupOrder->cashback_reseller,
          'total_shipping_charge' => $groupOrder->total_shipping_charge,
          'total_shipping_discount' => $groupOrder->total_shipping_discount,
          'total_shipping_nett' => $groupOrder->total_shipping_nett,
          'total_payment_charge' => $groupOrder->total_payment_charge,
          'tax_amount' => $groupOrder->tax_amount,
          'total_amount_summary' => $groupOrder->total_amount_summary,
          'total_pv' => $groupOrder->total_pv,
          'total_xv' => $groupOrder->total_xv,
          'total_bv' => $groupOrder->total_bv,
          'total_rv' => $groupOrder->total_rv,
          'total_order_to_shipped' => $groupOrder->total_order_to_shipped,
          'total_order_to_picked_up' => $groupOrder->total_order_to_picked_up,
          'status' => $groupOrder->status,
          'created_by' => $user,
          'transaction_date' => $groupOrder->transaction_date,
        ];

        // Insert into order_group_headers
        $groupOrderNew = new OrderGroupHeader($newGroupOrder);
        $groupOrderNew->save();
        $groupOrders[] = $newGroupOrder;


        // New Order Group Payments
        foreach ($groupOrder->payments as $payment) {
          $newGroupPayments = [
            'uuid' => Str::uuid(),
            'order_group_header_temp_uuid' => $payment->order_group_header_temp_uuid,
            'order_group_header_uuid' => $groupOrderUuid,
            'payment_type_uuid' => $payment->payment_type_uuid,
            'amount' => $payment->amount,
            'remarks' => $payment->remarks,
            'created_by' => $payment->created_by,
            'updated_by' => $user,
          ];

          // Insert into order_details
          $groupPaymentsNew = new OrderGroupPayment($newGroupPayments);
          $groupPaymentsNew->save();
          $groupPayments[] = $newGroupPayments;
        }

        // Orders
        foreach ($groupOrder->headers as $order) {
          // New Order Header;
          $headerUuid = Str::uuid();
          $transactionDate = $order->transaction_date;

          $newOrderHeader = [
            'uuid' => $headerUuid,
            'order_header_temp_uuid' => $order->uuid,
            'order_group_header_uuid' => $groupOrderUuid,
            'order_group_header_temp_uuid' => $groupOrder->uuid,
            'price_code_uuid' => $order->price_code_uuid,
            'member_uuid' => $order->member_uuid,
            'remarks' => $order->remarks,
            'total_discount_value' => $order->total_discount_value,
            'total_discount_value_amount' => $order->total_discount_value_amount,
            'total_voucher_amount' => $order->total_voucher_amount,
            'total_amount' => $order->total_amount,
            'total_amount_after_discount' => $order->total_amount_after_discount,
            'total_cashback' => $order->total_cashback,
            'total_cashback_reseller' => $order->total_cashback_reseller,
            'total_shipping_charge' => $order->total_shipping_charge,
            'total_shipping_discount' => $order->total_shipping_discount,
            'total_shipping_nett' => $order->total_shipping_nett,
            'total_payment_charge' => $order->total_payment_charge,
            'tax_amount' => $order->tax_amount,
            'total_charge' => $order->total_charge,
            'total_amount_summary' => $order->total_amount_summary,
            'total_pv' => $order->total_pv,
            'total_xv' => $order->total_xv,
            'total_bv' => $order->total_bv,
            'total_rv' => $order->total_rv,
            // 'total_pv_plan_joy' => $order->total_pv_plan_joy,
            // 'total_bv_plan_joy' => $order->total_bv_plan_joy,
            // 'total_rv_plan_joy' => $order->total_rv_plan_joy,
            // 'total_pv_plan_biz' => $order->total_pv_plan_biz,
            // 'total_bv_plan_biz' => $order->total_bv_plan_biz,
            // 'total_rv_plan_biz' => $order->total_rv_plan_biz,
            // 'total_price_joy' => $order->total_price_joy,
            // 'total_price_biz' => $order->total_price_biz,
            // 'total_price_with_bv' => $order->total_price_with_bv,
            'ship_type' => $order->ship_type,
            'status' => 1,
            'airway_bill_no' => $order->airway_bill_no,
            'created_by' => $user,
            'transaction_date' => $transactionDate,
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
              'uuid' => Str::uuid(),
              'order_header_uuid' => $headerUuid,
              'order_group_header_uuid' => $groupOrderUuid,
              'order_group_header_temp_uuid' => $groupOrder->uuid,
              'order_detail_temp_uuid' => $orderDetail->uuid,
              'product_price_uuid' => $orderDetail->product_price_uuid,
              'product_uuid' => $orderDetail->product_uuid,
              'qty' => $orderDetail->qty,
              'price' => $orderDetail->price,
              'discount_type' => $orderDetail->discount_type,
              'discount_value' => $orderDetail->discount_value,
              'discount_value_amount' => $orderDetail->discount_value_amount,
              'price_after_discount' => $orderDetail->price_after_discount,
              'cashback' => $orderDetail->cashback,
              'cashback_reseller' => $orderDetail->cashback_reseller,
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
              'uuid' => Str::uuid(),
              'order_payment_temp_uuid' => $orderPayment->uuid,
              'order_header_uuid' => $headerUuid,
              'order_group_header_uuid' => $groupOrderUuid,
              'order_group_header_temp_uuid' => $groupOrder->uuid,
              'payment_type_uuid' => $orderPayment->payment_type_uuid,
              'voucher_uuid' => $orderPayment->voucher_uuid,
              'amount' => $orderPayment->amount,
              'discount' => $orderPayment->discount,
              'amount_after_discount' => $orderPayment->amount_after_discount,
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
              'uuid' => Str::uuid(),
              'order_shipping_temp_uuid' => $shipping->uuid,
              'order_header_uuid' => $orderHeader->uuid,
              'courier_uuid' => $shipping->courier_uuid,
              'shipping_charge' => $orderHeader->total_shipping_charge,
              'receiver_name' => $orderHeader->receiver_name,
              'receiver_phone_number' => $orderHeader->receiver_phone_number,
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
            'uuid' => Str::uuid(),
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

          // Calculate Point member
          $helper = new Helper;
          $resultCalculation = $helper->confirmPaymentValid($headerUuid, $transactionDate);
        }
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
