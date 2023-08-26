<?php

namespace App\Models\Configs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'countries';
    protected $primaryKey = 'id';
    protected $fillable = [
        'uuid',
        'name',
        'name_iso',
        'region_name',
        'sub_region_name',
        'intermediate_region_name',
        'capital_city',
        'tld',
        'languages',
        'geoname_id',
        'dial_prefix',
        'alpha_3_isp',
        'alpha_2_isp',
        'corrency_code_iso',
        'currency_minro_unit_iso',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public $incrementing = false;

}
