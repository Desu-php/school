<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' =>$this->id,
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'lesson_block_id' =>$this->lesson_block_id,
            'task_type_id' =>$this->task_type_id,
            'user_task' =>$this->user_task,
            'status_task' => $this->status_task, //done, inactive
            'audio' => $this->getFilesPath('audio'),
            'video' => $this->getFilesPath('video', 'videos'),
            'gallery' => $this->gallery(),
            'video_iframe' => $this->video_iframe,
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'created_at' => $this->created_at,
        ];
    }
}
