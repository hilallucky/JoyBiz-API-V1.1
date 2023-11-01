<?php

namespace App\Models\Orders\Temporary;

use App\Models\Products\ProductAttribute;
use App\Models\Products\ProductGroupComposition;
use App\Models\Products\ProductPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetailTemp extends Model
{
  use HasFactory, SoftDeletes; //, Uuids;

  protected $table = 'order_details_temp';
  protected $primaryKey = 'id';

  protected $casts = [
    'qty' => 'integer',
    'price' => 'float',
    'discount_value' => 'float',
    'discount_value_amount' => 'float',
    'cashback' => 'float',
    'cashback_reseller' => 'float',
    'price_after_discount' => 'float',
    'pv' => 'float',
    'xv' => 'float',
    'bv' => 'float',
    'rv' => 'float',
  ];

  protected $fillable = [
    'id',
    'uuid',
    'order_group_header_temp_uuid',
    'order_header_temp_uuid',
    'product_uuid',
    'product_attribute_uuid',
    'product_price_uuid',
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

  public function group()
  {
    return $this->belongsToMany(
      ProductGroupComposition::class,
      'product_uuid',
      'uuid'
    );
  }
}
