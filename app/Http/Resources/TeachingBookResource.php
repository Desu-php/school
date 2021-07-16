<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeachingBookResource extends JsonResource
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
            'image' => $this->getFilesPath('image'),
            'audio' => $this->getFilesPath('audio'),
            'file' => $this->getFilesPath('file'),
            'status' => $this->deleted_at ? 'inactive' : 'active',
        ];
    }
}
