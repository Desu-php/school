<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskGallowResource extends JsonResource
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
            'word_array' => $this->word_array($this->word),
            'prompt' => $this->getTranslations('prompt'),
            'task_id' => $this->task_id,
        ];
    }
    public function word_array($word) {
        $arr = array();
        for ($i = 0; $i < strlen($word); $i++) {
            array_push($arr, ' ');
        }
        return $arr;
    }
}
