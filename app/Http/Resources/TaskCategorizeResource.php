<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskCategorizeResource extends JsonResource
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
            'prompt' => $this->getTranslations('prompt'),
            'task_id' => $this->task_id,
            'categories' => TaskCategorizeCategoryResource::collection($this->categories)
        ];
    }
}
