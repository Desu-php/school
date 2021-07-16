<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'question',
      'answer',
      'faq_category_id',
      'sort'
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
      'question',
      'answer'
    ];

    /**
     * The roles that belong to the user.
     */
    public function categories()
    {
      return $this->belongsToMany(FaqCategory::class, 'faqs_faq_categories');
    }
}
