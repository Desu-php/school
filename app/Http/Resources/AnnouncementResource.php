<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
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
            'title' => $this->getTranslations('title'),
            'description' => $this->getTranslations('description'),
            'is_show_in_home' =>$this->is_show_in_home,
            'video' => $this->getFilesPath('video'),
            'video_iframe' => $this->video_iframe,
            'teaching_language_id' => $this->teaching_language_id,
            'teaching_language' => $this->teaching_language,
            'status' => $this->deleted_at ? 'inactive' : 'active',
        ];
    }
}
