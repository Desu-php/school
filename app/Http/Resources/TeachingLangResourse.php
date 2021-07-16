<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeachingLangResourse extends JsonResource
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
            'code' => $this->code,
            'letters_array' => $this->letters_array,
            'letters' => $this->letters,
            'flag' => $this->getFlagPath(),
            'color' => $this->color,
            'announcement' => $this->announcement(),
            'announcement_course' => $this->announcement_course(),
            'status' => $this->deleted_at ? 'inactive' : 'active',
        ];
    }
}
