<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductFileAndImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_files_and_images';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'product_uuid',
        'original_file_name',
        'file_name',
        'size',
        'type',
        'domain',
        'path_file',
        'url',
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

    public function product()
    {
        return $this->belongsTo(
            Product::class,
            'product_uuid',
            'uuid'
        );
    }
}
