<?php

namespace App\Http\Controllers\Warehouses;

use App\Http\Controllers\Controller;
use App\Services\Warehouses\WarehouseService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    private WarehouseService $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function index(Request $request)
    {
        return $this->warehouseService->index($request);
    }

    public function store(Request $request)
    {
        return $this->warehouseService->store($request);
    }

    public function show(Request $request, $uuid)
    {
        return $this->warehouseService->show($request, $uuid);
    }

    public function updateBulk(Request $request)
    {
        return $this->warehouseService->updateBulk($request);
    }

    public function destroyBulk(Request $request)
    {
        return $this->warehouseService->destroyBulk($request);
    }
}
