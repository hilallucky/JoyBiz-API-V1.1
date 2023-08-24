<?php

namespace App\Models\Configs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $fillable = [
        'uuid',
        'ip',
        'domain_name',
        'status',
        'description',
        'phone',
        'country_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

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
}
