<?php

namespace App\Helpers\Products;

use App\Models\Products\ProductGroupComposition;
use Illuminate\Support\Facades\DB;

class GroupCompositionHelper
{
  //Get product attributes by product_uuid
  public function get($productUuid)
  {
    return ProductGroupComposition::where('product_uuid', $productUuid)->get();
  }

  //Get product attributes by product_uuid and multiplied with order qty
  public function multiply($productUuid, $qty)
  {
    return ProductGroupComposition::select(DB::raw('uuid', 'product_group_header_uuid', 'product_uuid', "qty * $qty"))
      ->where('product_uuid', $productUuid)->get();
  }
}
