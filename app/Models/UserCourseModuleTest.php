<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourseModuleTest extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'course_module_test_id',
        'status',
        'course_id',
    ];

    protected $with = [
        'course_module_test'
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course_module_test()
    {
        return $this->belongsTo(CourseModuleTest::class);
    }

}
