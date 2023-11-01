<?php

namespace App\Helpers\Products;

use Illuminate\Support\Facades\DB;

class GroupCompositionHelper
{
  //Get product attributes by product_uuid
  public function get($productUuid)
  {
    return ProductAttribute::where('product_uuid', $productUuid)->get();
  }

  //Get product attributes by product_uuid and multiplied with order qty
  public function multiply($productUuid, $qty)
  {
    $attributes= ProductAttribute::select(DB::raw('product_uuid', ))->where('product_uuid', $productUuid)->get();
  }
}
