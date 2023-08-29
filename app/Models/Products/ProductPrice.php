<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPrice extends Model
{
    use HasFactory, SoftDeletes; //, Uuids;

    protected $table = 'product_prices';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'product_uuid',
        'price_code_uuid',
        'status',
        'price',
        'discount_type',
        'discount_value',
        'discount_value_amount',
        'price_after_discount',
        'pv',
        'xv',
        'bv',
        'rv',
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

    public function priceCode()
    {
        return $this->belongsTo(
            PriceCode::class,
            'price_code_uuid',
            'uuid'
        );
    }

    public function product()
    {
        return $this->belongsTo(
            Product::class,
            'product_uuid',
            'uuid'
        );
    }
}
