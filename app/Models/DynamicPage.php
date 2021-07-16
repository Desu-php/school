<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class DynamicPage extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'key',
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'title'
    ];


    /**
     * The roles that belong to the user.
     */
    public function dynamic_page_texts()
    {
        return $this->hasMany(DynamicPageText::class);
    }
}
