<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Lesson extends Model
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
        'short_description',
        'video_iframe',
        'is_free',
        'course_module_id',
        'video_file'
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'name',
        'description',
        'short_description',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lesson_blocks ()
    {
        return $this->hasMany(LessonBlock::class, 'lesson_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course_module()
    {
        return $this->belongsTo(CourseModule::class);
    }

//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\HasMany
//     */
//    public function user_lesson()
//    {
//        return $this->hasOne(UserLesson::class);
//    }

    /**
     * @param $type
     *
     * @return string|null
     */
    public function getVideoFilePath()
    {
        if(!empty($this->video_file)) {
            return  asset('/storage/lesson/videos/' . $this->video_file);
        }

        return null;
    }

    /**
     * @param $type
     *
     * @return string|null
     */
    public function getStatus()
    {
        $user_lesson = UserLesson::where('lesson_id', $this->id)->where('user_id', auth('api')->id())->first();
        return $user_lesson ? $user_lesson->status : '';
    }
    /**
     * @param $type
     *
     * @return string|null
     */
    public function getUserTaskDone()
    {
        $user_task_done = UserTask::where('lesson_id', $this->id)->where('status', 'done')->where('user_id', auth('api')->id())->count();
        return $user_task_done ? $user_task_done : '';
    }
    /**
     * @param $type
     *
     * @return string|null
     */
    public function getUserTask()
    {
        $user_task = UserTask::where('lesson_id', $this->id)->where('user_id', auth('api')->id())->count();
        return $user_task ? $user_task : '';
    }

    /**
     * @return string
     */
    public function getUserLesson()
    {
        $user_lesson = UserLesson::where('lesson_id', $this->id)->where('user_id', auth('api')->id())->first();
        return $user_lesson ? $user_lesson : '';
    }


    public function getLessonMaxPoint()
    {
        $tasks = UserTask::where('lesson_id', $this->id)->where('user_id', auth('api')->id())->pluck('task_id');

        $course_max_point = [];
        foreach ($tasks as $id) {
            $data = Task::find($id);
            switch ($data->task_type_id) {
                case 1:
                    $task = TaskQuestion::where('task_id', $id)->count();
                    array_push($course_max_point, $task);
                    break;
                case 2:
                    $task = TaskMissingWord::where('task_id', $id)->count();
                    array_push($course_max_point, $task);
                    break;
                case 3:
                    $task = TaskPickUpTranslation::where('task_id', $id)->count();
                    array_push($course_max_point, $task);
                    break;
                case 4:
                    $task = TaskCategorize::where('task_id', $id)->count();
                    array_push($course_max_point, $task);
                    break;
                case 5:
                    $task = TaskComposeText::where('task_id', $id)->count();
                    array_push($course_max_point, $task);
                    break;
                case 6:
                    $task = TaskSpeakText::where('task_id', $id)->count();
                    array_push($course_max_point, $task);
                    break;
                case 8:
                    $task = TaskFieldOfDream::where('task_id', $id)->count();
                    array_push($course_max_point, $task);
                    break;
                case 9:
                    $task = TaskGallow::where('task_id', $id)->count();
                    array_push($course_max_point, $task);
                    break;
                case 10:
                    $task = TaskDeckCardQuestion::where('task_id', $id)->count();
                    array_push($course_max_point, $task);
                    break;
                case 11:
                    $task = TaskWheelFortuneQuestion::where('task_id', $id)->count();
                    array_push($course_max_point, $task);
                    break;
                case 12:
                    $task = TaskRememberFind::where('task_id', $id)->pluck('id');
                    $taskWord = TaskRememberFindWord::whereIn('remember_find_id', $task)->count();
                    array_push($course_max_point, $taskWord);
                    break;
                case 13:
                    $task = TaskSuggestedFromText::where('task_id', $id)->count();
                    array_push($course_max_point, $task);
                    break;
            }

        }
        $sum = array_sum($course_max_point);

        return $sum ? $sum : '0';

    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_lessons');
    }
}
