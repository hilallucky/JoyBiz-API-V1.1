<?php

namespace App\Models\Configs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cities';
    protected $primaryKey = 'id';
    protected $fillable = [
        'uuid',
        'country_uuid',
        'area_code',
        'zip_code',
        'province',
        'city',
        'district',
        'village',
        'latitude',
        'longitude',
        'elevation',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public $incrementing = false;
}
