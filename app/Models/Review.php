<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_MODERATION = 1;
    const STATUS_APPROVE = 2;
    const STATUS_REJECT = 3;

    /**
     * Return list of status codes and labels

     * @return array
     */
    public static function listStatus()
    {
        return [
            self::STATUS_MODERATION => 'moderation',
            self::STATUS_APPROVE => 'approve',
            self::STATUS_REJECT  => 'reject'
        ];
    }

    /**
     * @var array
     */
    protected $fillable = [
        'full_name',
        'email',
        'rating',
        'text',
        'status',
        'admin_id',
        'user_id'
    ];

    /**
     * @return mixed
     */
    public function status_name ()
    {
        return self::listStatus()[$this->status];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo('App\Models\Admin');
    }

}
