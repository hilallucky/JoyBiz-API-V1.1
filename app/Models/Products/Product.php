<?php

namespace App\Models\Products;

use App\Models\Utils\Gallery;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'id';


    protected $casts = [
        'is_register' => 'boolean',
        'status' => 'integer',
        'show_status' => 'boolean',
        'sc_show_status' => 'boolean',
        'weight' => 'float',
    ];
    
    protected $fillable = [
        'uuid',
        'category_uuid',
        'name',
        'description',
        'is_product_group',
        'is_register',
        'status',
        'show_status',
        'sc_show_status',
        'weight',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $visible = [
        'id',
        'category_uuid',
        'is_product_group',
        'uuid',
        'name',
        'description',
        'is_register',
        'status',
        'show_status',
        'sc_show_status',
        'weight',
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

    public function category()
    {
        return $this->belongsTo(
            ProductCategory::class,
            'category_uuid',
            'uuid'
        );
    }

    public function attributes()
    {
        return $this->hasMany(
            ProductAttribute::class,
            'product_uuid',
            'uuid'
        );
    }

    public function prices()
    {
        return $this->hasMany(
            ProductPrice::class,
            'product_uuid',
            'uuid'
        );
    }

    public function compositions()
    {
        return $this->belongsToMany(
            ProductGroupComposition::class,
            'product_uuid',
            'uuid'
        );
    }

    public function composition_by_header()
    {
        return $this->hasMany(
            ProductGroupComposition::class,
            'product_group_header_uuid',
            'uuid'
        );
    }

    // public function composition()
    // {
    //     return $this->hasMany(ProductGroupComposition::class, 'product_uuid', 'uuid');
    // }

    public function group()
    {
        return $this->belongsToMany(
            Product::class,
            'product_group_compositions',
            'product_group_header_uuid',
            'product_uuid'
        )->withPivot('qty');
    }

    public function images()
    {
        return $this->hasMany(
            Gallery::class,
            'link_uuid',
            'uuid'
        );
    }
}
