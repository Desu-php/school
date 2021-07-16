<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CourseModule extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'course_id',
    ];

    /**
     * The attributes for translation
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
//    protected  $appends = [
//        'status'
//    ];
    protected  $with = [
        'course'
    ];

//    public function getStatusAttribute()
//    {
//        return $this->user_module()->first() ? $this->user_module()->first()->status : '';
//    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_module()
    {
        return $this->hasMany(UserModule::class, 'module_id');
    }

    /**
     * @return string
     */
    public function getPointModule()
    {
        $user_lesson = UserModule::where('module_id', $this->id)->where('user_id', auth('api')->id())->first();
        return $user_lesson ? $user_lesson->point : '';
    }

    /**
     * @return string
     */
    public function getStatusModule()
    {
        $user_lesson = UserModule::where('module_id', $this->id)->where('user_id', auth('api')->id())->first();
        return $user_lesson ? $user_lesson->status : '';
    }
    /**
     * @return string
     */
    public function getTaskPoints()
    {
        $task_points = UserTask::where('module_id', $this->id)->where('user_id', auth('api')->id())->count();
        return $task_points ? $task_points : '';
    }
    /**
     * @return string
     */
    public function getTaskPointsDone()
    {
        $task_points_done = UserTask::where('status', 'done')->where('module_id', $this->id)->where('user_id', auth('api')->id())->count();
        return $task_points_done ? $task_points_done : '';
    }

    /**.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}
