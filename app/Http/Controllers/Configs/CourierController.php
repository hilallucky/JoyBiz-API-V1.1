<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use App\Services\Configs\CourierService;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    private CourierService $courierService;

    public function __construct(CourierService $courierService)
    {
        $this->courierService = $courierService;
    }

    //Get all courier
    public function index(Request $request)
    {
        return $this->courierService->index($request);
    }

    //Create new Courier
    public function store(Request $request)
    {
        return $this->courierService->store($request);
    }

    //Get Courier by ids
    public function show(Request $request, $uuid)
    {
        return $this->courierService->show($request, $uuid);
    }

    //UpdateBulk Courier
    public function updateBulk(Request $request)
    {
        return $this->courierService->updateBulk($request);
    }

    //Delete Courier by ids
    public function destroyBulk(Request $request)
    {
        return $this->courierService->destroyBulk($request);
    }
}
