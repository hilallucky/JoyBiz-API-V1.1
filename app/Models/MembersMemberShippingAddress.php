<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembersMemberShippingAddress extends Model
{    
    use HasFactory, SoftDeletes;

    protected $table = 'member_shipping_addresses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'uuid',
        'member_uuid',
        'city_uuid',
        'zip_code',
        'province',
        'city',
        'district',
        'village',
        'details',
        'notes',
        'remarks',
        'latitude',
        'longitude',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public $incrementing = false;
}
