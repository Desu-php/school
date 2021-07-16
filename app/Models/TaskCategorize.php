<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskCategorize extends Model
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

    /**
     * @var array
     */
    protected $with =[
        "categories"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany('App\Models\TaskCategorizeCategory', 'categorize_id');
    }
}
