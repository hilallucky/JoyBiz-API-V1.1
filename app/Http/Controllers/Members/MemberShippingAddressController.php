<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Services\Members\MemberShippingAddressService;
use Illuminate\Http\Request;

class MemberShippingAddressController extends Controller
{
  private MemberShippingAddressService $shippingAddressService;

  public function __construct(MemberShippingAddressService $shippingAddressService)
  {
    $this->shippingAddressService = $shippingAddressService;
  }

  //Get all Shipping Address Service informations
  public function index(Request $request)
  {
    return $this->shippingAddressService->index($request);
  }

  //Create new Shipping Address Service information
  public function store(Request $request)
  {
    return $this->shippingAddressService->store($request);
  }

  //Get Shipping Address Service information by ids
  public function show(Request $request, $uuid)
  {
    return $this->shippingAddressService->show($request, $uuid);
  }

  //Update Shipping Address Service information
  public function update(Request $request)
  {
    return $this->shippingAddressService->update($request);
  }

  //Update Shipping Address Service information
  public function updatePatch(Request $request)
  {
    return $this->shippingAddressService->updatePatch($request);
  }

  //Delete Shipping Address Service information by ids
  public function destroyBulk(Request $request)
  {
    return $this->shippingAddressService->destroyBulk($request);
  }
}
