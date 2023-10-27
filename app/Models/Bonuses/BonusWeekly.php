<?php

namespace App\Models\Bonuses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonusWeekly extends Model
{
    use SoftDeletes;

    protected $table = 'bonus_weeklies';
    protected $primaryKey = 'id';

    protected $cast = [
        'team' => 'float',
        'carry_forward' => 'float',
        'matching' => 'float',
        'total' => 'float',
        'ppn' => 'float',
        'wallet' => 'float',
        'voucher' => 'float',
        'total_transfer' => 'float',
    ];

    protected $fillable = [
        'wid',
        'user_uuid',
        'member_uuid',
        'express',
        'productivity',
        'team',
        'carry_forward',
        'matching',
        'total',
        'ppn',
        'wallet',
        'voucher',
        'total_transfer',
        'vouchered',
        'vouchered_by',
        'year'
    ];
    protected $hidden = [
        'id',
        'member_uuid',
        'created_at',
        'updated_at',
        'confirmed',
        'published',
        'confirmed_by',
        'published_by',
        'vouchered',
        'vouchered_by'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'uuid', 'user_uuid');
    }

    public function membership()
    {
        return $this->hasOne(Member::class, 'uuid', 'member_uuid');
    }

    // public function bonus_express()
    // {
    //     return $this->hasMany(BonusExpress::class, 'owner', 'owner')->with('transaction');
    // }

    // public function bonus_team()
    // {
    //     return $this->hasMany(BonusTeam::class, 'owner', 'owner');
    // }

    // public function bonus_matching()
    // {
    //     return $this->hasMany(BonusMatching::class, 'owner', 'owner');
    // }

    // public function periode()
    // {
    //     return $this->hasOne(Period::class, 'id', 'wid');
    // }

    // public function cf()
    // {
    //     return $this->hasOne(CarryForwardDetail::class, 'wid', 'wid');
    // }

    // #joybiz v1, dipakai BonusWeeklyAPI

    // public function team_bonus()
    // {
    //     return $this->hasMany(BonusTeam::class, 'owner', 'owner');
    // }

    // #joybiz v1
}
