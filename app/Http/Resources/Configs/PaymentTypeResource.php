<?php

namespace App\Http\Resources\Configs;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTypeResource extends JsonResource
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
            "uuid" => $this->uuid,
            "code" => $this->code,
            "name" => $this->name,
            "short_name" => $this->short_name,
            "description" => $this->description,
            "is_voucher" => $this->is_voucher,
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
