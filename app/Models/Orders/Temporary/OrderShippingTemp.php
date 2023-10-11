<?php

namespace App\Models\Orders\Temporary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderShippingTemp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_shipping_temp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'order_header_temp_uuid',
        'courier_uuid',
        'shipping_charge',
        'discount_shipping_charge',
        'member_address_uuid',
        'province',
        'city',
        'district',
        'village',
        'details',
        'notes',
        'remarks',
        'discount_shipping_charge',
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
