<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'member_addresses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'uuid',
        'member_uuid',
        'city_uuid',
        'zip_code',
        'province',
        'city',
        'district',
        'village',
        'details',
        'notes',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
        'position',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'uuid', 'member_uuid');
    }
}
