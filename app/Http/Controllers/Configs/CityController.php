<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use App\Services\Configs\CityService;
use Illuminate\Http\Request;

class CityController extends Controller
{
    private CityService $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    //Get all city
    public function index(Request $request)
    {
        return $this->cityService->index($request);
    }

    //Create new City
    public function store(Request $request)
    {
        return $this->cityService->store($request);
    }

    //Get City by ids
    public function show(Request $request, $uuid)
    {
        return $this->cityService->show($request, $uuid);
    }

    //UpdateBulk City
    public function updateBulk(Request $request)
    {
        return $this->cityService->updateBulk($request);
    }

    //Delete City by ids
    public function destroyBulk(Request $request)
    {
        return $this->cityService->destroyBulk($request);
    }

}
