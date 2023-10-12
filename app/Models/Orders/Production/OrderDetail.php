<?php

namespace App\Models\Orders\Production;

use App\Models\Products\Product;
use App\Models\Products\ProductPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'order_header_uuid',
        'order_details_temp_uuid',
        'product_price_uuid',
        'qty',
        'price',
        'discount_type',
        'discount_value',
        'discount_value_amount',
        'price_after_discount',
        'pv',
        'xv',
        'bv',
        'rv',
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


    public function productPrice()
    {
        return $this->belongsTo(
            ProductPrice::class,
            'product_price_uuid',
            'uuid'
        );
    }
}
