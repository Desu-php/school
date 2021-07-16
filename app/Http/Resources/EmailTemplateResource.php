<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailTemplateResource extends JsonResource
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
            'name' => $this->name,
            'blade_name' => $this->blade_name,
            'email_template_texts' => EmailTemplateTextResource::collection($this->email_template_texts),
            'status' => $this->deleted_at ? 'inactive' : 'active',
        ];
    }
}
