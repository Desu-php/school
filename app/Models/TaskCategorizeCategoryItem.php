<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskCategorizeCategoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        "name",
        "image",
        "categorize_category_id"
    ];
}
