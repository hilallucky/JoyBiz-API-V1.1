<?php

namespace App\Models\Bonuses\Joys;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoyBonusSummary extends Model
{
    use SoftDeletes;

    protected $table = 'joy_bonus_summaries';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'date',
        'wid',
        'member_uuid',
        'owner',
        'xpress',
        'bgroup',
        'leadership',
        'total',
        'tax',
        'voucher',
        'transfer',
        'confirmed',
        'published',
        'vouchered',
        'transfered',
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

    public function week()
    {
        return $this->hasOne(Period::class, 'id', 'wid');
    }
}
