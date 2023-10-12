<?php

namespace App\Models\Bonuses;

use App\Models\Members\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonusRank extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bonus_ranks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
        'process_uuid',
        'start_date',
        'end_date',
        'month',
        'year',
        'member_uuid',
        'rank_uuid',
        'rank_id',
        'sponsor_uuid',
        'ppv',
        'pgv',
        'gpv',
        'mid',
        'erank',
        'appv',
        'apgv',
        'jbp',
        'vj',
        'srank_uuid',
        'srank_id',
        'bj_active',
        'vj_active',
        'effective_rank_uuid',
        'effective_rank_id',
        'deleted_by',
    ];

    public function member()
    {
        return $this->hasOne(Member::class, 'uuid', 'member_uuid');
    }

    public function sponsor()
    {
        return $this->hasOne(Member::class, 'uuid', 'sponsor_uuid');
    }

    public function rank()
    {
        return $this->hasOne(rank::class, 'member_uuid', 'uuid');
    }
}
