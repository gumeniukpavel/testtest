<?php

namespace App\Db\Entity;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * Class User
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property integer $role_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property boolean $is_has_access_to_api
 * @property Carbon $end_access_to_api_at
 *
 * @property string $apiJWTToken
 *
 * @property Role $role
 * @property UserProfile $userProfile
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $visible = [
        'id',
        'name',
        'email',
        'is_has_access_to_api',
        'end_access_to_api_at',

        'apiJWTToken',

        'role',
        'userProfile'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_has_access_to_api',
        'end_access_to_api_at',
        'role_id'
    ];

    protected $appends = ['apiJWTToken'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getJWTToken()
    {
        return JWT::encode(
            [
                'id' => $this->id,
                'exp' => Carbon::now()->addMonths(6)->timestamp
            ],
            config('jwt.secret')
        );
    }

    public function getApiJWTTokenAttribute()
    {
        return $this->getJWTToken();
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function isAdmin(): bool
    {
        return $this->role->name == Role::ROLE_NAME_ADMIN;
    }
}
