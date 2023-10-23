<?php

namespace App\Models\Configs;

use App\Models\Bonuses\BonusRank;
use App\Models\Bonuses\BonusRankLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rank extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bonus_ranks';
    protected $primaryKey = 'id';

    protected $cast = [
        'rank_id' => 'integer',
        'acc_pbv' => 'integer',
        'status' => 'integer',
    ];

    protected $fillable = [
        'id',
        'uuid',
        'rank_id',
        'gallery_uuid',
        'name',
        'short_name',
        'description',
        'acc_pbv',
        'status',
        'deleted_by',
    ];

    public function bonus_ranks()
    {
        return $this->hasMany(BonusRank::class, 'rank_uuid', 'uuid');
    }

    public function bonus_rank_logs()
    {
        return $this->hasMany(BonusRankLog::class, 'rank_uuid', 'uuid');
    }
}
