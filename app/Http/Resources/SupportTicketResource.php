<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResource extends JsonResource
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
            'user' =>$this->user,
            'support_ticket_category' =>$this->support_ticket_category,
            'support_ticket_messages' => SupportTicketMessageResource::collection($this->support_ticket_messages),
            'unread_messages' => $this->unread_messages(),
            'created_at' => $this->created_at
        ];
    }
}
