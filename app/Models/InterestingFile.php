<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestingFile extends Model
{
    use HasFactory;

    /**
     * Media type constants
     */
    const TYPE_IMAGE = 'image';
    const TYPE_GALLERY = 'gallery';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';
    const TYPE_FILE = 'files';

    /**
     * @var array
     */
    protected  $fillable = [
        'name',
        'extension',
        'type',
        'interesting_id',
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type',  $type);
    }

}
