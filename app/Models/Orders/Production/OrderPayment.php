<?php

namespace App\Models\Orders\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_payments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'order_payments_temp_uuid',
        'order_header_uuid',
        'payment_type_uuid',
        'total_amount',
        'total_discount',
        'total_amount_after_discount',
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
