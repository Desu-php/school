<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeachingAudioResource extends JsonResource
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
            'audio' => $this->getAudioPath(),
            'status' => $this->deleted_at ? 'inactive' : 'active',
        ];
    }
}
