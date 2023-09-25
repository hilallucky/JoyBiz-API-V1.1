<?php

namespace App\Models\Calculations\Transactions;

use App\Models\Orders\Production\OrderHeader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalculationPointMember extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'calculation_point_members';
    protected $primaryKey = 'id';

    protected $casts = [
        'total_discount_value' => 'float',
        'total_discount_value_amount' => 'float',
        'total_price_after_discount' => 'float',
        'total_amount' => 'float',
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
        'start_date',
        'end_date',
        'member_uuid',
        'rank_uuid',
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

    public function member()
    {
        return $this->belongsTo(
            Member::class,
            'member_uuid',
            'uuid'
        );
    }

    public function orders()
    {
        return $this->belongsTo(
            OrderHeader::class,
            'uuid',
            'uuid'
        );
    }
}
