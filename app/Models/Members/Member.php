<?php

namespace App\Models\Members;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'phone',
        'sponsor_id',
        'sponsor_uuid',
        'placement_id',
        'placement_uuid',
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
        return $this->belongsTo(User::class,  'user_uuid', 'uuid');
    }

    public function sponsor()
    {
        return $this->belongsTo(Member::class, 'sponsor_id');
    }

    public function placement()
    {
        return $this->belongsTo(Member::class, 'placement_id');
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

    public function address()
    {
        return $this->hasMany(MemberAddress::class, 'member_uuid');
    }

    function getUplineCode($uuid, $type)
    {
        $member = Member::where('uuid', $uuid)->first();

        if (!$member) {
            return null; // Member not found
        }

        // Initialize an empty array to store the upline codes
        $uplines = [];

        // Start from the member and traverse up the hierarchy until the root
        while ($member) {
            $uplines[] = $member;
            $member = Member::where(
                'uuid',
                $type == 'placement' ? $member->placement_uuid : $member->sponsor_uuid
            )
                ->first();
        }

        // Reverse the array to get the upline codes from bottom to top
        $uplines = array_reverse($uplines);

        return $uplines;
    }

    function checkNetwork($uuid1, $uuid2)
    {
        $member = Member::where('uuid', $uuid1)->first();

        if (!$member || $member->placement_uuid) {
            return false; // Member not found
        }

        if ($member->uuid == $uuid2) {
            return true;
        }

        // Start from the member and traverse up the hierarchy until the root
        while ($member) {
            $member = Member::where('uuid', $member->placement_uuid)->first();

            if (!$member || $member->placement_uuid) {
                return false; // Member not found
            }

            if ($member->uuid == $uuid2) {
                return true;
            }
        }
    }
}
