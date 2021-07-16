<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskGallow extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable =[
        "word",
        "prompt",
        "task_id",
        "select_dictionary",
        "amount_numbers"
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'prompt'
    ];
}
