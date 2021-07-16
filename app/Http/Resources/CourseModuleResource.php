<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseModuleResource extends JsonResource
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
            'point' =>$this->getPointModule(),
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'course' => $this->course,
            'task_points' => $this->getTaskPoints(),
            'task_points_done' => $this->getTaskPointsDone(),
            'lessons' => \App\Http\Resources\Web\LessonResource::collection($this->lessons),
            'course_id' => $this->course_id,
            'status_module' => $this->getStatusModule() ? $this->getStatusModule() : 'inactive', //done, in_progress, inactive
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'created_at' => $this->created_at,
        ];
    }
}
