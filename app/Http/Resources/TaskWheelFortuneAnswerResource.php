<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskWheelFortuneAnswerResource extends JsonResource
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
            'task_wheel_fortune_question_id' => $this->task_wheel_fortune_question_id,
            'answer' => $this->getTranslations('answer'),
        ];
    }
}
