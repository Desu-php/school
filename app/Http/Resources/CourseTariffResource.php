<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseTariffResource extends JsonResource
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
            'price' => $this->price,
            'duration' => $this->duration,
            'automatic_check_tasks' => $this->automatic_check_tasks,
            'freezing_possibility' => $this->freezing_possibility,
            'access_independent_work' => $this->access_independent_work,
            'access_additional_materials' => $this->access_additional_materials,
            'additional_course_gift' => $this->additional_course_gift,
            'access_dictionary' => $this->access_dictionary,
            'access_grammar' => $this->access_grammar,
            'access_extend' => $this->access_extend,
            'access_notes' => $this->access_notes,
            'access_chat' => $this->access_chat,
            'access_fb_chat' => $this->access_fb_chat,
            'feedback_experts' => $this->feedback_experts,
            'access_upgrade_tariff' => $this->access_upgrade_tariff,
            'access_materials_after_purchasing_course' => $this->access_materials_after_purchasing_course,
            'discount_for_family' => $this->discount_for_family,
            'consultation' => $this->consultation,
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'created_at' => $this->created_at,
        ];
    }
}
