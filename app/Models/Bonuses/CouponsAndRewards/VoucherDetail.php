<?php

namespace App\Models\Bonuses\CouponsAndRewards;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherDetail extends Model
{ 
    use SoftDeletes;

    protected $table = 'voucher_details';
    protected $primaryKey = 'id';

    protected $casts = [
        'saldo' => 'float',
    ];

    protected $fillable = [
        'id',
        'uuid',
        'member_uuid',
        'code',
        'debit',
        'credit',
        'transaction_uuid',
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
