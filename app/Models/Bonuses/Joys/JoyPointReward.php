<?php

namespace App\Models\Bonuses\Joys;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoyPointReward extends Model
{
    use SoftDeletes;

    protected $table = 'joy_point_rewards';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'date',
        'member_uuid',
        'joy',
        'biz',
        'joy_rv',
        'biz_rv',
        'deleted_by',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'uuid', 'member_uuid');
    }

    public function membership()
    {
        return $this->hasOne(Member::class, 'uuid', 'member_uuid');
    }
}
