<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectSettingResources extends JsonResource
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
            'key' => $this->key,
            'value' => $this->field == 'json' ? json_decode($this->value, TRUE) : $this->value,
            'field' => $this->field,
            'is_default' => $this->is_default,
        ];
    }
}
