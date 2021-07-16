<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskQuestionResource extends JsonResource
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
            'answers' => TaskAnswerResource::collection($this->answers),
            'prompt' => $this->getTranslations('prompt'),
            'question' => $this->getTranslations('question'),
            'task_id' => $this->task_id,
        ];
    }
}
