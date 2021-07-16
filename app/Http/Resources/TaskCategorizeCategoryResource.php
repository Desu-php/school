<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskCategorizeCategoryResource extends JsonResource
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
            'name' => $this->name,
            'image' => $this->image ? explode(":", $this->image)[0] === 'https' ? $this->image : asset('storage/tasks/category/image/' . $this->image ) : null,
            'categorize_id' => $this->categorize_id,
            'category_items' => TaskCategorizeCategoryItemResource::collection($this->category_items)
        ];
    }
}
