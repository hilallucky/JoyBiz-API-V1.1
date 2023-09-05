<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\Products\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        return $this->productService->index($request);
    }

    //Strore data product with prices and variants
    public function storeIncludePrices(Request $request)
    {
        return $this->productService->storeIncludePrices($request);
    }

    //Get product information by ids
    public function show(
        $uuid,
        Request $request
    ) {
        return $this->productService->show($uuid, $request);
    }

    //UpdateBulk product information
    public function updateBulk(Request $request)
    {
        return $this->productService->updateBulk($request);
    }

    public function destroyBulk(Request $request)
    {
        return $this->productService->destroyBulk($request);
    }
}
