<?php

namespace App\Models\Configs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{
    
    use HasFactory, SoftDeletes;

    protected $table = 'payment_types';
    protected $primaryKey = 'id';
    protected $fillable = [
        'uuid',
        'ref_uuid',
        'code',
        'name',
        'description',
        'status',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public $incrementing = false;
}
