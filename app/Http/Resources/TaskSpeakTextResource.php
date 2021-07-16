<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskSpeakTextResource extends JsonResource
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
            'video_iframe' => $this->video_iframe,
            'prompt' => $this->getTranslations('prompt'),
            'task_id' => $this->task_id,
            'answer_text' => $this->answer_text,
            'video' => $this->getFilesPath('video'),
            'audio' => $this->getFilesPath('audio')
        ];
    }
}
