<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Course extends Model
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
        'image',
        'teaching_language_id',
        'course_level_id',
        'announcement_id',
        'course_type',
        'is_free',
        'is_free_lesson',
        'lesson_id'
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
     * @var array
     */
    protected $with = [
        'teaching_language',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'image_path',
        'type'
    ];


    /**
     * @return string|null
     */
    public function getImagePath()
    {
        if(!empty($this->image)) {
            return asset('/storage/course/images/' . $this->image);
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getImagePathAttribute()
    {
        if(!empty($this->image)) {
            return asset('/storage/course/images/' . $this->image);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getTypeAttribute()
    {
        return 'course';
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teaching_language ()
    {
        return $this->belongsTo(TeachingLanguage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course_level ()
    {
        return $this->belongsTo(CourseLevel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function announcement ()
    {
        return $this->belongsTo(Announcement::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tariffs()
    {
        return $this->belongsToMany(CourseTariff::class, 'course_tariffs_courses');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function course_modules()
    {
        return $this->hasMany(CourseModule::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function course_test()
    {
        return $this->hasMany(CourseTest::class);
    }

    /**
     *
     */
    public function buy()
    {
        $buyCoursesId = UserCourse::where('user_id', auth('api')->id())->get()->pluck('course_id');
        if (in_array($this->id, $buyCoursesId->all())){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function course_chat ()
    {
        return $this->hasOne(CourseChat::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_courses')->withPivot('id');
    }
}
