<?php

namespace App\Models\Users;

use App\Models\Members\Member;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'validation_code',
        'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Indicates if the IDs are UUID's.
     *
     * @var bool
     */
    public $incrementing = false;

    // public static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {

    //         $nodeProvider = new RandomNodeProvider();

    //         /* validate duplicate UUID */
    //         do {

    //             $uuid = Uuid::uuid1($nodeProvider->getNode());

    //             $uuid_exist = self::where('uuid', $uuid)->exists();

    //         } while ($uuid_exist);

    //         $model->uuid = $uuid;
    //     });
    // }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    //One user id has one or more member id's
    public function members()
    {
        return $this->hasMany(
            Member::class,
            'user_uuid',
            'uuid'
        );
    }

    public function member()
    {
        return $this->hasOne(
            Member::class,
            'user_uuid',
            'uuid'
        );
    }
}
