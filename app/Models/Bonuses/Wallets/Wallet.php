<?php

namespace App\Models\Bonuses\Wallets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes;

    protected $table = 'wallets';
    protected $primaryKey = 'id';

    protected $casts = [
        'saldo' => 'float',
    ];

    protected $fillable = [
        'id',
        'uuid',
        'member_uuid',
        'saldo',
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
