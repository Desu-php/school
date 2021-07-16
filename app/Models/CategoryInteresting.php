<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CategoryInteresting extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'color',
        'sort',
        'seo_title',
        'seo_description',
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'title',
        'seo_title',
        'seo_description'
    ];
}
