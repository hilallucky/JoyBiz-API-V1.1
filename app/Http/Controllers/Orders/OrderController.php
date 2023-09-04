<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Services\Orders\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
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
}
