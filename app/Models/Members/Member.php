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
        'uuid',
        'first_name',
        'last_name',
        'password',
        'phone',
        'sponsor_id',
        'user_uuid',
        'status'
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
}
