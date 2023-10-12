<?php

namespace App\Models\Bonuses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonusRankLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bonus_ranks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'process_uuid',
        'member_uuid',
        'rank_uuid',
        'deleted_by',
    ];

    public function member()
    {
        return $this->hasOne(Member::class, 'uuid', 'member_uuid');
    }
}
