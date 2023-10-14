<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceCode extends Model
{
    use HasFactory, SoftDeletes; //, Uuids;

    protected $table = 'price_codes';
    protected $primaryKey = 'id';

    protected $casts = [
        'status' => 'integer',
    ];

    protected $fillable = [
        'id',
        'uuid',
        'code',
        'name',
        'description',
        'status',
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
