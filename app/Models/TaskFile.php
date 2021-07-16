<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskFile extends Model
{
    use HasFactory;

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
        'name',
        'extension',
        'type',
        'task_id'
    ];

    /**
     * @var array
     */
    protected $appends = ['file_path'];

    public function getFilePathAttribute()
    {
        $images = [];
        if ($this->name) {
            foreach(self::IMAGE_SIZES as $key => $size) {
                $images[$key] = asset('/storage/tasks/gallery/' . $this->name . '_' . $key . '.' . $this->extension);
            }
            return $images;
        }
        return null;
    }
}
