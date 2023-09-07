<?php

namespace App\Http\Resources\Members;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            // "id" => $this->id,
            "uuid" => $this->uuid,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "password" => $this->password,
            "phone" => $this->phone,
            "sponsor_id" => $this->sponsor_id,
            "sponsor_uuid" => $this->sponsor_uuid,
            "placement_id" => $this->placement_id,
            "placement_uuid" => $this->placement_uuid,
            "user_uuid" => $this->user_uuid,
            "status" => $this->status,
            "created_by" => $this->created_by,
            "created_at" => $this->created_at,
            "updated_by" => $this->updated_by,
            "updated_at" => $this->updated_at,
            "deleted_by" => $this->deleted_by,
            "deleted_at" => $this->deleted_at,
        ];
    }
}
