<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceCodeResource extends JsonResource
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
      "code" => $this->code,
      "name" => $this->name,
      "description" => $this->description,
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
