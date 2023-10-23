<?php

namespace App\Http\Resources\Members;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberShippingAddressResource extends JsonResource
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
      "member_uuid" => $this->member_uuid,
      "receiver_name" => $this->receiver_name,
      "receiver_phone" => $this->receiver_phone,
      "city_uuid" => $this->city_uuid,
      "zip_code" => $this->zip_code,
      "province" => $this->province,
      "city" => $this->city,
      "district" => $this->district,
      "village" => $this->village,
      "details" => $this->details,
      "notes" => $this->notes,
      "remarks" => $this->remarks,
      "latitude" => $this->latitude,
      "longitude" => $this->longitude,
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
