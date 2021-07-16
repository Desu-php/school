<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskWheelFortuneQuestionResource extends JsonResource
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
            'task_id' => $this->task_id,
            'question' => $this->getTranslations('question'),
            'prompt' => $this->getTranslations('prompt'),
            'answers' => TaskWheelFortuneAnswerResource::collection($this->answers),
        ];
    }
}
