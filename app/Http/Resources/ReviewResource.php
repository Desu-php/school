<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'full_name' => $this->full_name,
            'email' => $this->email,
            'rating' => $this->rating,
            'text' => $this->text,
            'answer' => $this->answer,
            'status_name' => $this->status_name(),
            'user' => $this->user,
            'admin' => $this->admin,
            'deleted' => $this->deleted_at ? true : false,
        ];
    }
}
