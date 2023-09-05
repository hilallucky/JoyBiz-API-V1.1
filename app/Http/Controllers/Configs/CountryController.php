<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use App\Services\Configs\CountryService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    private CountryService $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    //Get all country
    public function index(Request $request)
    {
        return $this->countryService->index($request);
    }

    //Create new Country
    public function store(Request $request)
    {
        return $this->countryService->store($request);
    }

    //Get Country by ids
    public function show(Request $request, $uuid)
    {
        return $this->countryService->show($request, $uuid);
    }

    //UpdateBulk Country
    public function updateBulk(Request $request)
    {
        return $this->countryService->updateBulk($request);
    }

    //Delete Country by ids
    public function destroyBulk(Request $request)
    {
        return $this->countryService->destroyBulk($request);
    }
}
