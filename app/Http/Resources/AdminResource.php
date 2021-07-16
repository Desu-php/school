<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'avatar' => $this->avatarPath(),
            'role_id' => $this->role_id,
            'role_name' => ucfirst(join(" ", explode("_",$this->role->name))),
            'role' => $this->role,
            'status' => $this->deleted_at ? 'inactive' : 'active',
        ];
    }
}
