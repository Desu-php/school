<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'name',
      'sort'
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = ['name'];

    /**
     * The roles that belong to the user.
     */
    public function faqs()
    {
      return $this->belongsToMany(Faq::class, 'faqs_faq_categories');
    }
}
