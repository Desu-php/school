<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CourseModuleTest extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'course_module_id'
    ];

    /**
     * @var array
     */
    public $translatable = [
        'title'
    ];

    protected $appends = [
        'course_module',
        'test_task'
    ];

    /**
     * @return string
     */
    public function getCourseModuleAttribute () {
        $module =  CourseModule::where('id', $this->course_module_id)->first();
        return $module;
    }

    /**
     * @return string
     */
    public function getTestTaskAttribute () {
        $test = Task::where('module_test_id', $this->id)->first();
        return $test;
    }
}
