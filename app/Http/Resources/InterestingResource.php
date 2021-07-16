<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InterestingResource extends JsonResource
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
            'short_description' => $this->getTranslations('short_description'),
            'seo_title' => $this->getTranslations('seo_title'),
            'seo_description' => $this->getTranslations('seo_description'),
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'category_interesting_id' => $this->category_interesting_id,
            'category_interesting' => new CategoryInterestingResource($this->category_interesting),
            'video_iframe' => $this->video_iframe,
            'gallery' => $this->getPhotoFilesPaths('gallery'),
            'image' => $this->getPhotoFilesPaths('image'),
            'video' => $this->getMediaFilesPaths('video'),
            'audio' => $this->getMediaFilesPaths('audio'),
            'files' => $this->getFilesPaths('files'),
            'next' => $this->nextInteresting(),
            'previous' => $this->previousInteresting(),
        ];
    }
}



