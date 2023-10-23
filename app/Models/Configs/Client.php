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

}
