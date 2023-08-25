<?php

namespace App\Models\Products;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class Product extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'product_category_uuid',
        'name',
        'description',
        'status',
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

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $nodeProvider = new RandomNodeProvider();

            /* validate duplicate UUID */
            do {
                $uuid = Uuid::uuid1($nodeProvider->getNode());

                $uuid_exist = self::where('uuid', $uuid)->exists();
            } while ($uuid_exist);

            $model->uuid = $uuid;
        });
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_uuid', 'uuid');
    }
}
