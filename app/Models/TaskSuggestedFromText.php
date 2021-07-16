<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskSuggestedFromText extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable =[
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

    protected $with =[
        "words"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function words()
    {
        return $this->hasMany('App\Models\TaskSuggestedFromTextWord', 'suggested_text_id');
    }
}
