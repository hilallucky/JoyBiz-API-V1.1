<?php

namespace App\Models\Products;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes; //, Uuids;

    protected $table = 'product_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
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

    // public static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {

    //         $nodeProvider = new RandomNodeProvider();

    //         /* validate duplicate UUID */
    //         do {

    //             $uuid = Uuid::uuid1($nodeProvider->getNode());

    //             $uuid_exist = self::where('uuid', $uuid)->exists();
    //         } while ($uuid_exist);

    //         $model->uuid = $uuid;
    //     });
    // }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_uuid', 'uuid');
    }
}
