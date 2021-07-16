<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Interesting extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    const IMAGE_SIZES = [
        'image' => [
            'full' => [800, 600],
            'medium' => [600, 400],
            'small' => [320, 250],
        ],
        'gallery' => [
            'full' => [800, 600],
            'medium' => [600, 400],
            'small' => [320, 250],
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'category_interesting_id',
        'short_description',
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
        'description',
        'short_description',
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
        return 'interesting';
    }
    /**
     * @param $type
     *
     * @return array|null
     */
    public function getPhotoFilesPaths($type)
    {
        $files_paths = [];
        $files = $this->files()->ofType($type)->get();
        if(!empty($files) && $files->count()) {
            foreach($files as $k=>$file) {
                foreach(self::IMAGE_SIZES[$type] as $key => $size) {
                    if ($type === 'gallery') {
                        $files_paths[$k][$key] = [
                            'id' => $file->id,
                            'path' => asset('/storage/interesting/'.$type.'/' . $file->name . '_' . $key . '.' . $file->extension),
                        ];
                    } else {
                        $files_paths = [
                            'id' => $file->id,
                            'path' => asset('/storage/interesting/'.$type.'/' . $file->name . '_' . $key . '.' . $file->extension),
                        ];
                    }
                }
            }
            return $files_paths;
        }

        return null;
    }

    /**
     * @param $type
     *
     * @return array|null
     */
    public function getMediaFilesPaths($type)
    {
        $media_files_paths = [];
        $files = $this->files()->ofType($type)->get();
        if(!empty($files) && $files->count()) {
            foreach($files as $key => $file) {
                $media_files_paths = [
                    'id' => $file->id,
                    'path' => asset('/storage/interesting/'.$type.'/' . $file->name . '.' . $file->extension)
                ];
            }
            return $media_files_paths;
        }

        return null;
    }

    /**
     * @param $type
     *
     * @return array|null
     */
    public function getFilesPaths($type)
    {
        $media_files_paths = [];
        $files = $this->files()->ofType($type)->get();
        if(!empty($files) && $files->count()) {
            foreach($files as $key => $file) {
                array_push($media_files_paths, [
                    'id' => $file->id,
                    'path' => asset('/storage/interesting/'.$type.'/' . $file->name . '.' . $file->extension),
                    'name' => $file->name
                ]);
            }
            return $media_files_paths;
        }

        return null;
    }


    /**
     * The gallery that belong to the interesting.
     */
    public function files()
    {
        return $this->hasMany('App\Models\InterestingFile');
    }


    /**
     * The roles that belong to the user.
     */
    public function category_interesting()
    {
        return $this->belongsTo('App\Models\CategoryInteresting');
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function nextInteresting()
    {
        return self::where('id', '>',  $this->id)->orderBy('id', 'asc')->first();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public  function previousInteresting()
    {
        return self::where('id', '<', $this->id)->orderBy('id', 'desc')->first();
    }
}

