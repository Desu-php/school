<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CourseVideo extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'course_id',
        'image',
        'video',
        'video_iframe'
    ];

    /**
     * @var array
     */
    public $translatable = [
        'name',
        'description'
    ];

    /**
     * @param $type
     * @return string|null
     */
    public function getFilesPath($type)
    {
        if(!empty($this[$type])) {
            return  asset('/storage/course/'.$type.'/' . $this[$type]);
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getImagePath()
    {
        if(!empty($this->image)) {
            return asset('/storage/course/video-image/' . $this->image);
        }

        return null;
    }
}
