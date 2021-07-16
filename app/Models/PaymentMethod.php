<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class PaymentMethod extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'image',
        'sort',
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public $translatable = [
        'name',
        'description',
    ];

    protected $appends = ['image_path'];

    /**
     * @return string|null
     */
    public function getImagePathAttribute()
    {
        if(!empty($this->image)) {
            return asset('/storage/payment-methods/' . $this->image);
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getImagePath()
    {
        if(!empty($this->image)) {
            return asset('/storage/payment-methods/' . $this->image);
        }

        return null;
    }
}
