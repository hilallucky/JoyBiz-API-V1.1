<?php

namespace App\Http\Resources\WMS;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
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
            "id" => $this->id,
            "uuid" => $this->uuid,
            "code" => $this->code,
            "name" => $this->name,
            "phone" => $this->phone,
            "mobile_phone" => $this->mobile_phone,
            "email" => $this->email,
            "province" => $this->province,
            "city" => $this->city,
            "district" => $this->district,
            "village" => $this->village,
            "details" => $this->details,
            "description" => $this->description,
            "notes" => $this->notes,
            "remarks" => $this->remarks,
            "status" => $this->status,
            "created_by" => $this->created_by,
            "updated_by" => $this->updated_by,
            "deleted_by" => $this->deleted_by,
        ];
    }
}
