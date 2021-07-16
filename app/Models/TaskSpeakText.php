<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskSpeakText extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable =[
        "video_iframe",
        "video",
        "audio",
        "task_id",
        "prompt",
        "answer_text"
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'prompt'
    ];



    /**
     * @param $type
     * @return string|null
     */
    public function getFilesPath($type)
    {
        if(!empty($this[$type])) {
            return  asset('/storage/tasks/speak_text/'.$type.'/' . $this[$type]);
        }

        return null;
    }

}
