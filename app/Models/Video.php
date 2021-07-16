<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Video extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    const IMAGE_SIZES = [
        'full' => [900, 400],
        'medium' => [600, 300],
        'small' => [300, 200],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'title',
      'description',
      'image',
      'image_alt',
      'video_iframe',
      'video',
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
      'title',
      'description',
    ];

    /**
     * @return array|null
     */
    public function getImagePaths()
    {
        $images = [];
        if(!empty($this->image)) {
            foreach(self::IMAGE_SIZES as $key => $size) {
                $images[$key] = asset('/storage/video/gallery/' . $this->image . '_' . $key . '.' . $this->image_extension);
            }
            return $images;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getVideoPath()
    {
        if(!empty($this->video)) {
            return asset('/storage/video/videos/' . $this->video);
        }

        return null;
    }
}
