<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Task extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable =[
        "name",
        "description",
        "lesson_block_id",
        "test_id",
        "module_test_id",
        "task_type_id",
        'status_task',
        'video_iframe',
        'video',
        "audio",
    ];

    /**
     * @var array
     */
    public $translatable = [
        'name',
        'description'
    ];

    protected $with = [
        'user_task'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function task_file()
    {
        return $this->hasMany('App\Models\TaskFile');
    }

    public function user_task()
    {
        return $this->hasOne(UserTask::class)->where('user_id',auth('api')->id());
    }

    /**
     * @param $type
     * @return string|null
     */
    public function getFilesPath($type, $path = false)
    {
        if ($path){
            $dir = $path;
        }else{
            $dir = $type;
        }
        if(!empty($this[$type])) {
            return  asset('/storage/tasks/'.$dir.'/' . $this[$type]);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function gallery()
    {
        return $this->task_file()->get();
    }
}
