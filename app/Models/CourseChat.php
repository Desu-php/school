<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'count_persons',
        'age',
        'gender',
        'course_id',
        'status',
        'chat_type'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function course_chat_messages ()
    {
        return $this->hasMany(CourseChatMessage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users ()
    {
        return $this->belongsToMany(User::class,'course_chat_user');
    }
}
