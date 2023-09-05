<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\Products\ProductCategoryService;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    private ProductCategoryService $productCategoryService;

    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }

    //Get all product informations
    public function index(Request $request)
    {
        return $this->productCategoryService->index($request);
    }

    //Create new product information
    public function store(Request $request)
    {
        return $this->productCategoryService->store($request);
    }

    //Get product category information by ids
    public function show(Request $request, $uuid)
    {
        return $this->productCategoryService->show($request, $uuid);
    }

    //UpdateBulk product category information
    public function updateBulk(Request $request)
    {
        return $this->productCategoryService->updateBulk($request);
    }

    //Delete product information by ids
    public function destroyBulk(Request $request)
    {
        return $this->productCategoryService->destroyBulk($request);
    }
}
