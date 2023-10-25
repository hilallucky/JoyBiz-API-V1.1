<?php

namespace App\Models\Orders\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderShipping extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_shippings';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'order_shipping_temp_uuid',
        'order_header_uuid',
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
