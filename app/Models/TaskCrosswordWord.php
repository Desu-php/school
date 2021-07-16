<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskCrosswordWord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        "word",
        "question",
        "crossword_id"
    ];
}
