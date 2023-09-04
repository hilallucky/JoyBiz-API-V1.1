<?php

namespace App\Models\Orders\Temporary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use HasFactory, SoftDeletes; //, Uuids;

    protected $table = 'order_details_temp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'order_header_temp_uuid',
        'product_price_uuid',
        'qty',
        'price',
        'discount_type',
        'discount_value',
        'discount_value_amount',
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
}
