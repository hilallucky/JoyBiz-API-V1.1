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

    protected $casts = [
        'status' => 'integer',
        'price' => 'float',
        'discount_value' => 'float',
        'discount_value_amount' => 'float',
        'price_after_discount' => 'float',
        'cashback' => 'float',
        'cashback_reseller' => 'float',
        'shipping_budget' => 'float',
        'pv' => 'float',
        'xv' => 'float',
        'bv' => 'float',
        'rv' => 'float',
    ];

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
        'cashback',
        'cashback_reseller',
        'shipping_budget',
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
