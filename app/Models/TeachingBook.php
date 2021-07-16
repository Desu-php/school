<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TeachingBook extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'image', 'audio', 'file'
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'name',
        'description'
    ];

    public function getFilesPath($type)
    {
        if(!empty($this[$type])) {
            return  asset('/storage/teaching-book/'.$type.'/' . $this[$type]);
        }

        return null;
    }
}
