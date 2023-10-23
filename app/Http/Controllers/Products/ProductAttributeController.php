<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\Products\ProductAttributeService;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    private ProductAttributeService $productAttributeService;

    public function __construct(ProductAttributeService $productAttributeService)
    {
        $this->productAttributeService = $productAttributeService;
    }

    //Get all product attribute informations
    public function index(Request $request)
    {
        return $this->productAttributeService->index($request);
    }

    //Create new product attribute information
    public function store(Request $request)
    {
        return $this->productAttributeService->store($request);
    }

    //Get product attribute information by ids
    public function show(Request $request, $uuid)
    {
        return $this->productAttributeService->show($request, $uuid);
    }

    //UpdateBulk product attribute information
    public function updateBulk(Request $request)
    {
        return $this->productAttributeService->updateBulk($request);
    }

    //Delete product attribute information by ids
    public function destroyBulk(Request $request)
    {
        return $this->productAttributeService->destroyBulk($request);
    }
}
