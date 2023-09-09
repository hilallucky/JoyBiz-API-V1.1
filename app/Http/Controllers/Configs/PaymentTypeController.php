<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use App\Services\Configs\PaymentTypeService;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    
    private PaymentTypeService $paymentTypeService;

    public function __construct(PaymentTypeService $paymentTypeService)
    {
        $this->paymentTypeService = $paymentTypeService;
    }

    //Get all paymentType
    public function index(Request $request)
    {
        return $this->paymentTypeService->index($request);
    }

    //Create new PaymentType
    public function store(Request $request)
    {
        return $this->paymentTypeService->store($request);
    }

    //Get PaymentType by ids
    public function show(Request $request, $uuid)
    {
        return $this->paymentTypeService->show($request, $uuid);
    }

    //UpdateBulk PaymentType
    public function updateBulk(Request $request)
    {
        return $this->paymentTypeService->updateBulk($request);
    }

    //Delete PaymentType by ids
    public function destroyBulk(Request $request)
    {
        return $this->paymentTypeService->destroyBulk($request);
    }
}
