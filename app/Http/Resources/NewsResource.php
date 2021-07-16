<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
          'short_description' => $this->getTranslations('short_description'),
          'description' => $this->getTranslations('description'),
          'seo_title' => $this->getTranslations('seo_title'),
          'seo_description' => $this->getTranslations('seo_description'),
          'image_paths' => $this->getImagePaths(),
          'video_path' => $this->getVideoPath(),
          'next' => $this->nextNews(),
          'previous' => $this->previousNews(),
          'image_alt' => $this->image_alt,
          'video_iframe' => $this->video_iframe,
          'status' => $this->deleted_at ? 'inactive' : 'active',
          'created_at' => $this->created_at,
        ];
    }
}
