<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductGroupHeader extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'product_group_headers';
    protected $primaryKey = 'id';

    protected $casts = [
        'status' => 'integer',
    ];
    
    protected $fillable = [
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

    protected $visible = [
        'id',
        'uuid',
        'product_uuid',
        'name',
        'description',
        'status',
        'remarks',
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

    public function product()
    {
        return $this->hasOne(
            Product::class,
            'product_uuid',
            'uuid'
        );
    }

    public function compositions()
    {
        return $this->hasMany(
            ProductGroupComposition::class,
            'product_group_header_uuid',
            'uuid'
        );
    }

}
