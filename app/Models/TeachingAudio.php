<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TeachingAudio extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'audio'
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'name',
        'description'
    ];

    /**
     * @param $type
     *
     * @return string|null
     */
    public function getAudioPath()
    {
        if(!empty($this->audio)) {
            return  asset('/storage/teaching-audios/' . $this->audio);
        }

        return null;
    }

}
