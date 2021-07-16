<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TaskTranslation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        "translation",
        "phrase",
        "pick_up_translation_id"
    ];
}
