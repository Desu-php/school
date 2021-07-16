<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseVideoResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' =>$this->id,
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'course_id' =>$this->course_id,
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'image' => $this->getImagePath(),
            'video' => $this->getFilesPath('video'),
            'video_iframe' => $this->video_iframe,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
