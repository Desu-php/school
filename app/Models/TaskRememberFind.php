<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskRememberFind extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable =[
        "prompt",
        "task_id"
    ];

    protected $with =[
        "words"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function words()
    {
        return $this->hasMany('App\Models\TaskRememberFindWord', 'remember_find_id');
    }

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'prompt'
    ];
}
