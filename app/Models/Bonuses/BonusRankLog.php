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
    'start_date',
    'end_date',
    'month',
    'year',
    'member_uuid',
    'rank_uuid',
    'rank_id',
    'sponsor_uuid',
    'sponsor_id',
    'ppv',
    'pgv',
    'ppv_effective_rank',
    'gpv_effective_rank',
    'mid',
    'effective_rank_uuid',
    'effective_rank_id',
    'appv',
    'apbv',
    'jbo',
    'bj',
    'vj',
    'current_rank_uuid',
    'current_rank_id',
    'bj_active',
    'vj_active',
    'bonus_effective_rank_uuid',
    'bonus_effective_rank_id',
    'updated_by',
    'deleted_by',
  ];

  public function member()
  {
    return $this->hasOne(Member::class, 'uuid', 'member_uuid');
  }
}
