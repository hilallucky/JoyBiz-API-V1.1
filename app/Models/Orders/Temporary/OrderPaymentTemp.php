<?php

namespace App\Models\Orders\Temporary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPaymentTemp extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'order_payments_temp';
  protected $primaryKey = 'id';
  protected $casts = [
    'amount' => 'float',
    'discount' => 'float',
    'amount_after_discount' => 'float',
  ];


  protected $fillable = [
    'id',
    'uuid',
    'order_group_header_temp_uuid',
    'order_header_temp_uuid',
    'payment_type_uuid',
    'voucher_uuid',
    'voucher_code',
    'amount',
    'discount',
    'amount_after_discount',
    'remarks',
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
}
