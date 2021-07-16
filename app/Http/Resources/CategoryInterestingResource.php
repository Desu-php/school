<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryInterestingResource extends JsonResource
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
            'seo_title' => $this->getTranslations('seo_title'),
            'seo_description' => $this->getTranslations('seo_description'),
            'sort' => $this->sort,
            'slug' => $this->slug,
            'color' => $this->color ? $this->color : $this->teaching_language->color,
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'created_at' => $this->created_at,
        ];
    }
}
