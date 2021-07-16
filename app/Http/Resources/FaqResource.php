<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
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
        'question' => $this->getTranslations('question'),
        'answer' => $this->getTranslations('answer'),
        'categories' => $this->categories,
        'sort' => $this->sort,
        'status' => $this->deleted_at ? 'inactive' : 'active',
      ];
    }
}
