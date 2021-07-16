<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TeachingLanguage extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'color',
        'letters',
        'flag',
        'code',
    ];

    /**
     * @var array
     */
    public $translatable = [
        'name'
    ];

    /**
     * @return string
     */

    protected $appends = [
        'flag_path',
        'letters_array'
    ];


    public function getFlagPathAttribute()
    {
        return asset('/storage/languages/flags/' . $this->flag);
    }

    public function getLettersArrayAttribute()
    {
        return preg_split("/[\s,]*[\s,]/",  $this->letters);
    }

    public function getFlagPath()
    {
        return asset('/storage/languages/flags/' . $this->flag);
    }

    public function announcement_course()
    {
        if (auth('api')->id()) {
            $user_course = UserCourse::where('user_id', auth('api')->id())->pluck('course_id');
            $course = Course::whereNotIn('id', $user_course)->whereHas('announcement', function ($query) {
                if ($query) {
                    $query->where('is_show_in_home', 'true');
                }
            })->first();
        } else {
            $course = Course::whereHas('announcement', function ($query) {
                if ($query) {
                    $query->where('is_show_in_home', 'true');
                }
            })->first();
        }

        return $course;
    }

    /**
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function announcement()
    {
        return $this->announcements()->where('is_show_in_home', 'true')->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function announcements ()
    {
        return $this->hasMany(Announcement::class);
    }
}

