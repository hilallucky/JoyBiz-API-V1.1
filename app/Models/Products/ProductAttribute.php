<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttribute extends Model
{

    use HasFactory, SoftDeletes; //, Uuids;

    protected $table = 'product_attributes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'product_uuid',
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

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_uuid', 'uuid');
    }
}
