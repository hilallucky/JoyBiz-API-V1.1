<?php

namespace App\Http\Resources\Configs;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            "country_uuid" => $this->country_uuid,
            "area_code" => $this->area_code,
            "zip_code" => $this->zip_code,
            "province" => $this->province,
            "city" => $this->city,
            "district" => $this->district,
            "village" => $this->village,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "elevation" => $this->elevation,
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
