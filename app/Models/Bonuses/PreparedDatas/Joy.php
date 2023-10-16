<?php

namespace App\Models\Bonuses\PreparedDatas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Joy extends Model
{
    use SoftDeletes;

    protected $table = 'sranks';
    protected $primaryKey = 'id';

    protected $casts = [
        'ppv' => 'integer',
        'pbv' => 'integer',
        'pro' => 'integer',
        'tpvj' => 'integer',
        'tbvj' => 'integer',
        'gpvj' => 'integer',
        'gbvj' => 'integer',
        'srank' => 'integer',
        'erank' => 'integer',
    ];

    protected $fillable = [
        'wid',
        'member_uuid', //jbid;
        'sponsor_uuid', //'spid',
        'placement_uuid', //'upid',
        'ppv',
        'pbv',
        'pro',
        'tpvj',
        'tbvj',
        'gpvj',
        'gbvj',
        'srank',
        'erank'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'uuid', 'member_uuid');
    }

    public function membership()
    {
        return $this->hasOne(Membership::class, 'uuid', 'member_uuid');
    }

    public function effectiveRank()
    {
        return $this->hasMany(ERank::class, 'member_uuid', 'member_uuid');
    }
}
