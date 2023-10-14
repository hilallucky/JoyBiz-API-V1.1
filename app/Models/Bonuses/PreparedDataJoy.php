<?php

namespace App\Models\Bonuses;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreparedDataJoy extends Model
{
    use SoftDeletes;

    protected $table = 'prepared_data_joys';
    protected $primaryKey = 'id';
    protected $casts = [
        'ppv' => 'integer',
        'pbv' => 'integer',
        'pro' => 'integer',
        'tpvj' => 'integer',
        'tbvj' => 'integer',
        'gpvj' => 'integer',
        'gbvj' => 'integer',
    ];

    protected $fillable = [
        'week_period_id',
        'member_uuid',
        'sponsor_uuid',
        'placement_uuid',
        'ppv',
        'pbv',
        'pro',
        'tpvj',
        'tbvj',
        'gpvj',
        'gbvj',
        'current_rank',
        'effective_rank'
    ];


    public function user()
    {
        return $this->hasOne(User::class, 'member_uuid', 'member_uuid');
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'uuid', 'member_uuid');
    }

    public function effectiveRank()
    {
        return $this->hasMany(BonusRank::class, 'member_uuid', 'member_uuid');
    }
}
