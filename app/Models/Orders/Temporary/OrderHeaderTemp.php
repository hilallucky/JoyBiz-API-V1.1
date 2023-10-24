<?php

namespace App\Models\Orders\Temporary;

use App\Models\Members\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderHeaderTemp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_headers_temp';
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
        'order_group_header_temp_uuid',
        'uuid',
        'price_code_uuid',
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
        'ship_type',
        'status',
        'airway_bill_no',
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

    public function details()
    {
        return $this->hasMany(
            OrderDetailTemp::class,
            'order_header_temp_uuid',
            'uuid'
        );
    }

    public function payments()
    {
        return $this->hasMany(
            OrderPaymentTemp::class,
            'order_header_temp_uuid',
            'uuid'
        );
    }

    public function shipping()
    {
        return $this->hasMany(
            OrderShippingTemp::class,
            'order_header_temp_uuid',
            'uuid'
        );
    }

    public function groupOrderTemp()
    {
        return $this->hasOne(
            OrderShippingTemp::class,
            'order_header_temp_uuid',
            'uuid'
        );
    }
}
