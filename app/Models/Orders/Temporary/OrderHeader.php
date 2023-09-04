<?php

namespace App\Models\Orders\Temporary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderHeader extends Model
{
    use HasFactory, SoftDeletes; //, Uuids;

    protected $table = 'order_headers_temp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'price_code_uuid',
        'member_uuid',
        'remarks',
        'total_discount_value',
        'total_discount_value_amount',
        'total_price_after_discount',
        'total_amount',
        'total_shipping_charge',
        'total_payment_charge',
        'total_amount_summary',
        'total_pv',
        'total_xv',
        'total_bv',
        'total_rv',
        'status',
        'airway_bill_no',
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

    public function details()
    {
        return $this->hasMany(
            OrderDetail::class,
            'order_header_temp_uuid',
            'uuid'
        );
    }

    public function payments()
    {
        return $this->hasMany(
            OrderPayment::class,
            'order_header_temp_uuid',
            'uuid'
        );
    }

    public function shipping()
    {
        return $this->hasMany(
            OrderShipping::class,
            'order_header_temp_uuid',
            'uuid'
        );
    }
}
