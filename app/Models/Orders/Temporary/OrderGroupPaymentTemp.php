<?php

namespace App\Models\Orders\Temporary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderGroupPaymentTemp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_group_payments_temp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'order_group_header_temp_uuid',
        'payment_type_uuid',
        'amount',
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
