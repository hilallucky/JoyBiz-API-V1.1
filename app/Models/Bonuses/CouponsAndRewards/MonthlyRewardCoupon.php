<?php

namespace App\Models\Bonuses\CouponsAndRewards;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlyRewardCoupon extends Model
{
    use SoftDeletes;

    protected $table = 'monthly_reward_coupons';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'member_uuid',
        'voucher',
        'mid',
        'active',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'uuid', 'member_uuid');
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'uuid', 'member_uuid');
    }
}
