<?php

namespace App\Models\Bonuses\Ranks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SRank extends Model
{
    use SoftDeletes;

    protected $table = 'sranks';
    protected $primaryKey = 'id';

    protected $casts = [
        'ppv' => 'integer',
        'gpv' => 'integer',
    ];

    protected $fillable = [
        'id',
        'uuid',
        'member_uuid',
        'sponsor_uuid',
        'placement_uuid',
        'appv',
        'apbv',
        'jbp',
        'bj',
        'vj',
        'srank',
        'bj_active',
        'vj_active',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'uuid', 'member_uuid');
    }
}
