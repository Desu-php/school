<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class LessonBlock extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'lesson_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany('App\Models\LessonBlockFile');
    }

    /**
     * @var array
     */
    public $translatable = [
        'name',
        'description',
    ];

    /**
     * The attributes for translation
     * @var array
     */
    protected $with = [
        'tasks',
        'user_lesson_block'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks ()
    {
        return $this->hasMany(Task::class, 'lesson_block_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user_lesson_block ()
    {
        return $this->hasOne(UserLessonBlock::class,'lesson_block_id')->where('user_id', auth('api')->id());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function gallery()
    {
        return $this->files()->where('type', 'gallery')->get();
    }
}
