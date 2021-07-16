<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DynamicPageTextResource extends JsonResource
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
            'id' => $this->id,
            'description' => $this->getTranslations('description'),
            'dynamic_page' => $this->dynamic_page,
            'is_current' => $this->is_current,
            'status' => $this->deleted_at ? 'inactive' : 'active',
        ];
    }
}
