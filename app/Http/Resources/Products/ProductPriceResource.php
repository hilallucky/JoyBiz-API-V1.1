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
            // "product_uuid" => $this->product_uuid,
            'price_code' => $this->whenLoaded(
                'priceCode',
                function () {
                    return [
                        "uuid" => $this->priceCode->uuid,
                        "code" => $this->priceCode->code,
                        "name" => $this->priceCode->name,
                    ];
                }
            ),
            "status" => $this->status,
            "remarks" => $this->remarks,
            "price" => $this->price,
            "discount_type" => $this->discount_type,
            "discount_value" => $this->discount_value,
            "discount_value_amount" => $this->discount_value_amount,
            "price_after_discount" => $this->price_after_discount,
            "pv" => $this->pv,
            "xv" => $this->xv,
            "bv" => $this->bv,
            "rv" => $this->rv,
            'product_category' => $this->whenLoaded(
                'category',
                function () {
                    return [
                        "uuid" => $this->category->uuid,
                        "code" => $this->category->code,
                        "name" => $this->category->name,
                        "description" => $this->category->description,
                    ];
                }
            ),
            // "created_by" => $this->created_by,
            // "created_at" => $this->created_at,
            // "updated_by" => $this->updated_by,
            // "updated_at" => $this->updated_at,
            // "deleted_by" => $this->deleted_by,
            // "deleted_at" => $this->deleted_at,
        ];
    }

    public function relationToArray($request)
    {
        return [
            "uuid" => $this->uuid,
            'price_code' => $this->whenLoaded(
                'priceCode',
                function () {
                    return [
                        "uuid" => $this->priceCode->uuid,
                        "code" => $this->priceCode->code,
                        "name" => $this->priceCode->name,
                    ];
                }
            ),
            "price" => $this->price,
            "discount_type" => $this->discount_type,
            "discount_value" => $this->discount_value,
            "discount_value_amount" => $this->discount_value_amount,
            "price_after_discount" => $this->price_after_discount,
            "pv" => $this->pv,
            "xv" => $this->xv,
            "bv" => $this->bv,
            "rv" => $this->rv,
            'product_category' => $this->whenLoaded(
                'category',
                function () {
                    return [
                        "uuid" => $this->category->uuid,
                        "code" => $this->category->code,
                        "name" => $this->category->name,
                        "description" => $this->category->description,
                    ];
                }
            ),
        ];
    }

}
