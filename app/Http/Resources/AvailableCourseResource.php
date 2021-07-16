<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AvailableCourseResource extends JsonResource
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
            'image_path' => $this->getImagePath(),
            'teaching_language' => new TeachingLangResourse($this->teaching_language),
            'course_level' => new CourseLevelResource($this->course_level),
            'course_level_id' => $this->course_level_id,
            'tariffs' => $this->tariffs,
            'buy' => $this->buy(),
            'is_free' => $this->is_free,
            'course_type' => $this->course_type,
            'announcement' => new AnnouncementResource($this->announcement),
        ];
    }
}
