<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CourseLevel extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable =[
        "name"
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'name',
    ];
}
