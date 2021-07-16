<?php

namespace App\Models;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'login',
        'phone',
        'birthday',
        'gender',
        'avatar',
        'password',
        'site_id'
    ];

    protected $appends = ['avatar_path'];

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

    /**
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * @return string|null
     */
    public function getAvatarPathAttribute()
    {
        return $this->avatar ? asset('/storage/users/avatars/' . $this->avatar) : null;
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        ResetPassword::createUrlUsing(function ($user, $token) {
            return env('WEB_URL') . '/reset-password?token='.$token.'&email='.$user->email;
        });
        $this->notify(new ResetPassword($token));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_module()
    {
        return $this->hasMany(UserModule::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function course_chat ()
    {
        return $this->belongsToMany(CourseChat::class,'course_chat_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lessons ()
    {
        return $this->belongsToMany(Lesson::class, 'user_lessons');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function chat_expert ()
    {
        return $this->belongsToMany(ChatExpert::class, 'chat_experts');
    }
}
