<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Announcement extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'is_show_in_home',
        'video',
        'video_iframe',
        'teaching_language_id',
    ];

    /**
     * @var array
     */
    public $translatable = [
        'title',
        'description',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_show_in_home' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teaching_language ()
    {
        return $this->belongsTo(TeachingLanguage::class);
    }

    /**
     * @param $type
     * @return string|null
     */
    public function getFilesPath($type)
    {
        if(!empty($this[$type])) {
            return  asset('/storage/announcement/'.$type.'/' . $this[$type]);
        }

        return null;
    }
}
