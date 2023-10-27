<?php

namespace App\Models\Bonuses\Ranks;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ERank extends Model
{
    use SoftDeletes;

    protected $table = 'eranks';
    protected $primaryKey = 'id';

    protected $casts = [
        'ppv' => 'integer',
        'gpv' => 'integer',
    ];

    protected $fillable = [
        'id',
        'uuid',
        'member_uuid',
        'sponsor_uuid',
        'placement_uuid',
        'ppv',
        'gpv',
        'mid',
        'erank',
        'erank_uuid',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'uuid', 'member_uuid');
    }
}
