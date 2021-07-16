<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CourseTariff extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
        'duration',
        'automatic_check_tasks',
        'freezing_possibility',
        'access_independent_work',
        'access_additional_materials',
        'additional_course_gift',
        'access_dictionary',
        'access_grammar',
        'access_notes',
        'access_chat',
        'access_fb_chat',
        'access_extend',
        'feedback_experts',
        'access_upgrade_tariff',
        'access_materials_after_purchasing_course',
        'discount_for_family',
        'consultation',
    ];

    public $translatable = [
        'name',
    ];

    public $casts = [
        'access_extend' => 'array'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_tariffs_courses');
    }
}
