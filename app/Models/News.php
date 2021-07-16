<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class News extends Model
{
  use HasFactory, HasTranslations, SoftDeletes;

  const IMAGE_SIZES = [
    'full' => [900, 400],
    'medium' => 300,
    'small' => 80,
  ];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'title',
    'short_description',
    'description',
    'image',
    'image_extension',
    'image_alt',
    'video',
    'video_iframe',
    'seo_title',
    'seo_description',
  ];

  /**
   * The attributes for translation
   * @var array
   */
  public $translatable = [
    'title',
    'short_description',
    'description',
    'seo_title',
    'seo_description',
  ];

    /**
     * @var array
     */
    protected $appends = [
        'type'
    ];

    /**
     * @return string
     */
    public function getTypeAttribute()
    {
        return 'news';
    }
    /**
     * @return array|null
     */
  public function getImagePaths()
  {
    $images = [];
    if(!empty($this->image)) {
      foreach(self::IMAGE_SIZES as $key => $size) {
        $images[$key] = asset('/storage/news/images/' . $this->image . '_' . $key . '.' . $this->image_extension);
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
          return asset('/storage/news/videos/' . $this->video);
      }

      return null;
  }


    /**
     * @param $id
     *
     * @return mixed
     */
    public function nextNews()
    {
        return self::where('id', '>',  $this->id)->orderBy('id', 'asc')->first();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public  function previousNews()
    {
        return self::where('id', '<', $this->id)->orderBy('id', 'desc')->first();
    }

}
