<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'avatar' => $this->avatar,
            'avatar_path' => $this->avatar_path,
            'birthday' => $this->birthday,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'first_name' => $this->first_name,
            'gender' => $this->gender,
            'login' => $this->login,
            'locked' => $this->locked,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'site_id' => $this->site_id,
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }
}
