<?php

namespace App\Models\Orders\Production;

use App\Models\Orders\Temporary\OrderGroupHeaderTemp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderGroupHeader extends Model
{

  use HasFactory, SoftDeletes;

  protected $table = 'order_group_headers';
  protected $primaryKey = 'id';

  protected $casts = [
    'total_discount_value' => 'float',
    'total_discount_value_amount' => 'float',
    'total_price_after_discount' => 'float',
    'total_amount' => 'float',
    'total_voucher_amount' => 'float',
    'total_amount_after_discount' => 'float',
    'total_cashback' => 'float',
    'total_cashback_reseller' => 'float',
    'total_shipping_charge' => 'float',
    'total_shipping_discount' => 'float',
    'total_shipping_nett' => 'float',
    'total_payment_charge' => 'float',
    'tax_amount' => 'float',
    'total_charge' => 'float',
    'total_amount_summary' => 'float',
    'total_pv' => 'float',
    'total_xv' => 'float',
    'total_bv' => 'float',
    'total_rv' => 'float',
  ];

  protected $fillable = [
    'id',
    'uuid',
    'order_group_header_temp_uuid',
    'member_uuid',
    'remarks',
    'total_discount_value',
    'total_discount_value_amount',
    'total_voucher_amount',
    'total_amount',
    'total_amount_after_discount',
    'total_cashback',
    'total_cashback_reseller',
    'total_shipping_charge',
    'total_shipping_discount',
    'total_shipping_charge',
    'total_shipping_nett',
    'total_payment_charge',
    'tax_amount',
    'total_amount_summary',
    'total_pv',
    'total_xv',
    'total_bv',
    'total_rv',
    'total_order_to_shipped',
    'total_order_to_picked_up',
    'status',
    'created_by',
    'updated_by',
    'deleted_by',
    'transaction_date',
  ];

  /**
   * Indicates if the IDs are UUID's.
   *
   * @var bool
   */
  public $incrementing = false;

  public function member()
  {
    return $this->belongsTo(
      Member::class,
      'member_uuid',
      'uuid'
    );
  }

  public function getGroupOrderTemp()
  {
    return $this->hasOne(
      OrderGroupHeaderTemp::class,
      'uuid',
      'order_group_header_temp_uuid'
    );
  }

  public function headers()
  {
    return $this->hasMany(
      OrderHeader::class,
      'order_group_header_uuid',
      'uuid'
    );
  }

  public function payments()
  {
    return $this->hasMany(
      OrderGroupPayment::class,
      'order_group_header_uuid',
      'uuid'
    );
  }
}
