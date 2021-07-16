<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLessonBlock extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'lesson_id',
        'user_id',
        'lesson_block_id',
        'point',
    ];
}
