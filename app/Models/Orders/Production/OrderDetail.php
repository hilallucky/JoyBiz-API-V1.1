<?php

namespace App\Models\Orders\Production;

use App\Models\Products\Product;
use App\Models\Products\ProductAttribute;
use App\Models\Products\ProductGroupComposition;
use App\Models\Products\ProductPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class OrderDetail extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'order_details';
  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'uuid',
    'order_header_uuid',
    'order_group_header_uuid',
    'order_group_header_temp_uuid',
    'order_detail_temp_uuid',
    'product_price_uuid',
    'product_uuid',
    'product_attribute_uuid',
    'is_product_group',
    'qty',
    'price',
    'discount_type',
    'discount_value',
    'discount_value_amount',
    'cashback',
    'cashback_reseller',
    'price_after_discount',
    'pv',
    'xv',
    'bv',
    'rv',
    'status',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  /**
   * Indicates if the IDs are UUID's.
   *
   * @var bool
   */
  public $incrementing = false;

  public function productPrice()
  {
    return $this->belongsTo(
      ProductPrice::class,
      'product_price_uuid',
      'uuid'
    );
  }

  public function attribute()
  {
    return $this->hasMany(
      ProductAttribute::class,
      'uuid',
      'product_attribute_uuid'
    );
  }

  public function attributes()
  {
    return $this->hasMany(
      ProductAttribute::class,
      'uuid',
      'product_attribute_uuid'
    );
  }

  public function compositions()
  {
    return $this->belongsToMany(
      ProductGroupComposition::class,
      'product_uuid',
      'uuid'
    );
  }

  public function composition_by_header()
  {
    return $this->hasMany(
      ProductGroupComposition::class,
      'product_group_header_uuid',
      'uuid'
    );
  }

  public function group($productUuid, $qty)
  {
    return DB::table('product_group_compositions')
      ->selectRaw("product_group_compositions.uuid, product_group_compositions.product_group_header_uuid, product_group_compositions.product_uuid, product_group_compositions.qty * $qty as qty")
      ->selectRaw("products.product_header_uuid, products.name, products.description, products.is_register, products.status, products.weight * $qty as weight")
      ->leftJoin('products', 'products.uuid', '=', 'product_group_compositions.product_uuid')
      ->where('product_group_compositions.product_group_header_uuid', $productUuid)->get();
  }
}
