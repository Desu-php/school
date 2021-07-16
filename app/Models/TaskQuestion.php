<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskQuestion extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable =[
        "question",
        "prompt",
        "task_id"
    ];

    /**
     * @var array
     */
    protected $with = ['answers'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany('App\Models\TaskAnswer');
    }

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'question',
        'prompt'
    ];
}
