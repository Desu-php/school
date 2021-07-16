<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTaskPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'point',
        'code',
        'question_id',
    ];

}
