<?php

namespace App\Models\Orders\Production;

use App\Models\Members\Member;
use App\Models\Orders\Temporary\OrderHeaderTemp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderHeader extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_headers';
    protected $primaryKey = 'id';

    protected $casts = [
        'total_discount_value' => 'float',
        'total_discount_value_amount' => 'float',
        'total_price_after_discount' => 'float',
        'total_amount' => 'float',
        'total_voucher_amount' => 'float',
        'total_cashback' => 'float',
        'total_cashback_reseller' => 'float',
        'total_shipping_charge' => 'float',
        'total_payment_charge' => 'float',
        'total_amount_summary' => 'float',
        'total_pv' => 'float',
        'total_xv' => 'float',
        'total_bv' => 'float',
        'total_rv' => 'float',
    ];

    protected $fillable = [
        'id',
        'uuid',
        'order_header_temp_uuid',
        'price_code_uuid',
        'member_uuid',
        'remarks',
        'total_discount_value',
        'total_discount_value_amount',
        'total_price_after_discount',
        'total_amount',
        'total_voucher_amount',
        'total_cashback',
        'total_cashback_reseller',
        'total_shipping_charge',
        'total_payment_charge',
        'total_amount_summary',
        'total_pv',
        'total_xv',
        'total_bv',
        'total_rv',
        'status',
        'airway_bill_no',
        'calculation_point_members_uuid',
        'calculation_date',
        'created_by',
        'updated_by',
        'deleted_by',
        'approved_date',
        'approved_by',
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

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'created_by',
            'uuid'
        );
    }

    public function specialCustomer()
    {
        return $this->hasOne(Member::class, 'member_uuid', 'member_uuid')
            ->with(['address']);
    }

    public function headerTemp()
    {
        return $this->belongsTo(
            OrderHeaderTemp::class,
            'order_header_uuid',
            'uuid'
        );
    }

    public function details()
    {
        return $this->hasMany(
            OrderDetail::class,
            'order_header_uuid',
            'uuid'
        );
    }

    public function payments()
    {
        return $this->hasMany(
            OrderPayment::class,
            'order_header_uuid',
            'uuid'
        );
    }

    public function shipping()
    {
        return $this->hasMany(
            OrderShipping::class,
            'order_header_uuid',
            'uuid'
        );
    }
}
