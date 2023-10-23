<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\Products\PriceCodeService;
use Illuminate\Http\Request;

class PriceCodeController extends Controller
{
    private PriceCodeService $priceCodeService;

    public function __construct(PriceCodeService $priceCodeService)
    {
        $this->priceCodeService = $priceCodeService;
    }

    //Get all product informations
    public function index(Request $request)
    {
        return $this->priceCodeService->index($request);
    }

    //Create new product price information
    public function store(Request $request)
    {
        return $this->priceCodeService->store($request);
    }

    //Get product price information by ids
    public function show(Request $request, $uuid)
    {
        return $this->priceCodeService->show($request, $uuid);
    }

    //UpdateBulk product price information
    public function updateBulk(Request $request)
    {
        return $this->priceCodeService->updateBulk($request);
    }

    //Delete product price information by ids
    public function destroyBulk(Request $request)
    {
        return $this->priceCodeService->destroyBulk($request);
    }
}
