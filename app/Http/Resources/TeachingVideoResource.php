<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeachingVideoResource extends JsonResource
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
            'video_path' => $this->getVideoPath(),
            'image_paths' => $this->getImagePaths(),
            'video_iframe' => $this->video_iframe,
            'image_alt' => $this->image_alt,
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'created_at' => $this->created_at,
        ];
    }
}
