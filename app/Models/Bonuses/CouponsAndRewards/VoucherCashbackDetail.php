<?php

namespace App\Models\Bonuses\CouponsAndRewards;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherCashbackDetail extends Model
{
    use SoftDeletes;

    protected $table = 'voucher_cashback_details';
    protected $primaryKey = 'id';

    protected $casts = [
        'amount' => 'float',
    ];

    protected $fillable = [
        'id',
        'uuid',
        'member_uuid',
        'code',
        'credit',
        'amount',
        'encrypted_amount',
        'description',
        'transaction_uuid',
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
