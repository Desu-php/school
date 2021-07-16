<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class DynamicPageText extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'is_current',
        'dynamic_page_id'
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'description'
    ];

    /**
     * The roles that belong to the user.
     */
    public function dynamic_page()
    {
        return $this->belongsTo(DynamicPage::class);
    }
}
