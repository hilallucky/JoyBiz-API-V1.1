<?php

namespace App\Models\Configs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Courier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'couriers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'uuid',
        'gallery_uuid',
        'code',
        'name',
        'short_name',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public $incrementing = false;
}
