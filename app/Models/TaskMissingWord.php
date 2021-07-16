<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskMissingWord extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable =[
        "missing_words_text",
        "prompt",
        "task_id"
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'prompt'
    ];
}
