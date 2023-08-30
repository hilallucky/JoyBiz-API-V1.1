<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductGroupComposition extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'product_group_compositions';
    protected $primaryKey = 'id';

    protected $casts = [
        'qty' => 'integer',
    ];

    protected $fillable = [
        'uuid',
        'product_group_header_uuid',
        'product_uuid',
        'qty',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $visible = [
        'id',
        'uuid',
        'product_group_header_uuid',
        'product_uuid',
        'qty',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function product_group_header()
    {
        return $this->hasOne(
            ProductGroupHeader::class,
            'uuid',
            'product_group_header_uuid'
        );
    }

    public function product_source()
    {
        return $this->belongsTo(
            Product::class,
            'product_uuid',
            'uuid'
        );
    }

    // public function group()
    // {
    //     return $this->belongsTo(Product::class, 'group_uuid', 'uuid');
    // }

    public function product()
    {
        return $this->belongsTo(
            Product::class,
            'product_uuid',
            'uuid'
        );
    }

}
