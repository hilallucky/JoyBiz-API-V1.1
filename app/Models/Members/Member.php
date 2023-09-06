<?php

namespace App\Models\Members;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'uuid',
        'first_name',
        'last_name',
        'password',
        'phone',
        'sponsor_id',
        'sponsor_uuid',
        'upline_id',
        'upline_uuid',
        'user_uuid',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'position',
    ];
    protected $hidden = [
        'password',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uuid', 'user_uuid');
    }

    public function sponsor()
    {
        return $this->belongsTo(Member::class, 'sponsor_id');
    }

    public function leftLegMembers()
    {
        return $this->hasMany(Member::class, 'sponsor_id', 'id')->where('position', 'left');
    }

    public function rightLegMembers()
    {
        return $this->hasMany(Member::class, 'sponsor_id', 'id')->where('position', 'right');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'sponsor_id');
    }
}
