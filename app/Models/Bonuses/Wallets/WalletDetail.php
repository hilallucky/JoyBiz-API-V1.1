<?php

namespace App\Models\Bonuses\Wallets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletDetail extends Model
{
    use SoftDeletes;

    protected $table = 'wallet_details';
    protected $primaryKey = 'id';

    protected $casts = [
        'amount' => 'float',
    ];

    protected $fillable = [
        'id',
        'uuid',
        'member_uuid',
        'code',
        'debit',
        'credit',
        'note',
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
