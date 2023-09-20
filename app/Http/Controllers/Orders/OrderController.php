<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Services\Orders\OrderApprovalService;
use App\Services\Orders\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;
    private OrderApprovalService $orderAppService;

    public function __construct(
        OrderService $orderService,
        OrderApprovalService $orderAppService
    ) {
        $this->orderService = $orderService;
        $this->orderAppService = $orderAppService;
    }

    public function store(Request $request)
    {
        return $this->orderService->store($request);
    }

    // public function update(Request $request)
    // {
    //     $this->orderService->update($request->validated(), $user);

    //     return redirect()->route('users.index');
    // }

    public function getOrderTempList(Request $request)
    {
        return $this->orderService->getOrderList($request);
    }

    public function getOrderTempDetails($uuid)
    {
        return $this->orderService->getOrderDetails($uuid);
    }

    public function approveOrder(Request $request)
    {
        return $this->orderAppService->approveOrder($request);
    }

    public function getOrderApprovedList(Request $request)
    {
        return $this->orderAppService->getOrderList($request);
    }

    public function getOrderApprovedDetails($uuid)
    {
        return $this->orderAppService->getOrderDetails($uuid);
    }
}
