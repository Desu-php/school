<?php

namespace App\Http\Resources;

use App\Models\CourseModule;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' =>$this->id,
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'short_description' => $this->getTranslations('short_description'),
            'course_module' => $this->course_module,
            'video_iframe' => $this->video_iframe,
            'is_free' => $this->is_free,
            'lesson_blocks' => $this->lesson_blocks,
            'video_file' => $this->getVideoFilePath(),
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'created_at' => $this->created_at,
        ];
    }
}
