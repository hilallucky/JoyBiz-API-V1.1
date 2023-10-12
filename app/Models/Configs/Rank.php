<?php

namespace App\Models\Configs;

use App\Models\Bonuses\BonusRank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rank extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bonus_ranks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uuid',
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

}
