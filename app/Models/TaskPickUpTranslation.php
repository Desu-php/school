<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskPickUpTranslation extends Model
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
        "translations"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany('App\Models\TaskTranslation', 'pick_up_translation_id');
    }
}
