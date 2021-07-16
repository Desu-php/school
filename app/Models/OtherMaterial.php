<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class OtherMaterial extends Model
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
        'link'
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'name',
        'description',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany('App\Models\OtherMaterialFile');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function files_list()
    {
        return $this->files()->where('type', 'file')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function gallery()
    {
        return $this->files()->where('type', 'gallery')->get();
    }
}
