<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductPriceResource extends JsonResource
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
            "product_uuid" => $this->name,
            "price_code" => PriceCodeResource::collection($this->whenLoaded('attributes')),
            "varian" => ProductAttributeResource::collection($this->whenLoaded('attributes')),
            "status" => $this->status,
            "remarks" => $this->remarks,
            "prices" => ProductAttributeResource::collection($this->whenLoaded('attributes')),
            "created_by" => $this->created_by,
            "created_at" => $this->created_at,
            "updated_by" => $this->updated_by,
            "updated_at" => $this->updated_at,
            "deleted_by" => $this->deleted_by,
            "deleted_at" => $this->deleted_at,
            "product_category" => new ProductCategoryResource($this->when($this->relationLoaded('category'), $this->category)),
        ];
    }
}
