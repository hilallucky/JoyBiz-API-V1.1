<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatuses extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_statuses';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'order_header_uuid',
        'status',
        'reference_uuid',
        'description',
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
