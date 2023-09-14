<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'warehouses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'uuid',
        'code',
        'name',
        'phone',
        'mobile_phone',
        'email',
        'province',
        'city',
        'district',
        'village',
        'zip_code',
        'details',
        'description',
        'notes',
        'remarks',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
