<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskType extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable =[
        "name",
        "description",
        "image"
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
     * @return mixed
     */
    public function task()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * @return string|null
     */
    public function getImagePath()
    {
        if(!empty($this->image)) {
            return asset('/storage/task-type/image/' . $this->image);
        }

        return null;
    }
}
