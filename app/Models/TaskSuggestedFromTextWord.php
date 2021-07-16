<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskSuggestedFromTextWord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        "word",
        "number",
        "word_select",
        "suggested_text_id"
    ];

    protected $hidden = [
        'number'
    ];
}
