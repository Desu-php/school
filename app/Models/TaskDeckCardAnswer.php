<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskDeckCardAnswer extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable =[
        "answer",
        "correct_answer",
        "task_deck_card_question_id"
    ];

    /**
     * @var array
     */
    protected $casts=[
        "correct_answer" => "boolean"
    ];

    /**
     * @var array
     */
    public $translatable = [
        'answer'
    ];
}
