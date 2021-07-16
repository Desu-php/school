<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'user_id',
        'course_chat_id'
    ];


    public function user ()
    {
        return $this->belongsTo(User::class);

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course_chat()
    {
        return $this->belongsTo(CourseChat::class);
    }
}
