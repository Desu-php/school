<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskCategorizeCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        "name",
        "image",
        "categorize_id"
    ];

    /**
     * @var array
     */
    protected $with =[
        "category_items"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function category_items()
    {
        return $this->hasMany('App\Models\TaskCategorizeCategoryItem', 'categorize_category_id');
    }
}
