<?php

namespace App\Http\Resources\Configs;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            "name" => $this->country_uuid,
            "name_iso" => $this->area_code,
            "region_name" => $this->region_name,
            "sub_region_name" => $this->sub_region_name,
            "intermediate_region_name" => $this->intermediate_region_name,
            "capital_city" => $this->capital_city,
            "tld" => $this->tld,
            "languages" => $this->languages,
            "geoname_id" => $this->geoname_id,
            "dial_prefix" => $this->dial_prefix,
            "alpha_2_iso" => $this->alpha_2_iso,
            "alpha_3_iso" => $this->alpha_3_iso,
            "corrency_code_iso" => $this->corrency_code_iso,
            "currency_minor_unit_iso" => $this->currency_minor_unit_iso,
            "status" => $this->status,
            "remarks" => $this->remarks,
            "created_by" => $this->created_by,
            "created_at" => $this->created_at,
            "updated_by" => $this->updated_by,
            "updated_at" => $this->updated_at,
            "deleted_by" => $this->deleted_by,
            "deleted_at" => $this->deleted_at,
        ];
    }
}
