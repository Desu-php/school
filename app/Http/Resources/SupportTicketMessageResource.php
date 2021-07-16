<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketMessageResource extends JsonResource
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
            'message' => $this->message,
            'is_read' => $this->is_read,
            'sender_is_user' => $this->sender_is_user,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'admin_id' => $this->admin_id,
            'admin' => $this->admin,
            'created_at' => $this->created_at,
        ];
    }
}
