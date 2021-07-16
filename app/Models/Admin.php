<?php

namespace App\Models;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes;


    protected $with = ['role'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'name',
            'phone',
            'avatar',
            'role_id',
            'email',
            'password',
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
            'password',
        ];

    /**
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

    public function role()
    {
        return $this->belongsTo('App\Models\AdminRole');
    }
    public function avatarPath()
    {
        return $this->avatar ? asset('/storage/admin/avatars/' . $this->avatar) : null;
    }

    public function sendPasswordResetNotification($token)
    {
        // The trick is first to instantiate the notification itself
        $notification = new ResetPassword($token);
        // Then use the createUrlUsing method
        $notification->createUrlUsing(function ($token) {
            return 'http://acustomurl.lol';
        });
        // Then you pass the notification
        $this->notify($notification);
    }
}
