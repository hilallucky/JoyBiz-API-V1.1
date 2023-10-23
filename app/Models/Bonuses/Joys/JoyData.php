<?php

namespace App\Models\Bonuses\Joys;

use App\Models\Bonuses\BonusRank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoyData extends Model
{
    use SoftDeletes;

    protected $table = 'joy_datas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'date',
        'member_uuid',
        'sponsor_uuid',
        'upline_uuid',
        'ppv',
        'pbv',
        'prv',
        'gpv',
        'gbv',
        'grv',
        'pgpv',
        'pgbv',
        'jrank',
        // 'apbv',
        // 'deleted_by',
    ];

    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(Member::class, 'uuid', 'member_uuid');
    }

    public function membership()
    {
        return $this->hasOne(Member::class, 'uuid', 'member_uuid');
    }

    public function effectiveRank()
    {
        return $this->hasOne(BonusRank::class, 'member_uuid', 'member_uuid');
    }
}
