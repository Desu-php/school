<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'course_module' => $this->course_module,
            'video_iframe' => $this->video_iframe,
            'is_free' => $this->is_free,
            'user_lesson' => $this->getUserLesson(),
            'lesson_max_point' => $this->getLessonMaxPoint(),
            'user_task' => $this->getUserTask(),
            'user_task_done' => $this->getUserTaskDone(),
            'lesson_blocks' => $this->lesson_blocks,
            'status_lesson' =>  $this->getStatus(),
            'video_file' => $this->getVideoFilePath(),
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'created_at' => $this->created_at,
        ];
    }
}
