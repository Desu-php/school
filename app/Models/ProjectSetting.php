<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSetting extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'field',
        'is_default',
    ];
}
