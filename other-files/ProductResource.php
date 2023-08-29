<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            "name" => $this->name,
            "description" => $this->description,
            "varian" => ProductAttributeResource::collection($this->whenLoaded('attributes')),
            "status" => $this->status,
            "remarks" => $this->remarks,
            // "prices" => ProductPriceResource::collection($this->whenLoaded('prices')),
            'prices' => $this->whenLoaded('prices', function () {
                // return $this->prices;
                // [
                //     "uuid" => $this->prices->uuid,
                //     "price" => $this->prices->price,
                //     "discount_type" => $this->prices->discount_type,
                //     "discount_value" => $this->prices->discount_value,
                //     "discount_value_amount" => $this->prices->discount_value_amount,
                //     "price_after_discount" => $this->prices->price_after_discount,
                //     "pv" => $this->prices->pv,
                //     "xv" => $this->prices->xv,
                //     "bv" => $this->prices->bv,
                //     "rv" => $this->prices->rv,
                // ];

                return $this->prices->map(function ($prices) {
                    return [
                        // 'name' => $prices->uuid,
                        // 'created_at' => $prices->created_at,
                        "uuid" => $prices->uuid,
                        // "price_code" => $prices->price_code,
                        // 'price_code' => $this->whenLoaded('priceCode', function () {
                        //     return [
                        //         "uuid" => $this->priceCode,
                        //         "code" => $this->priceCode->code,
                        //         "name" => $this->priceCode->name,
                        //     ];
                        // }),
                        // "price_code" => new PriceCodeResource($this->when($this->relationLoaded('priceCode'), $this->priceCode)),


                        'price_code' => $this->whenLoaded('priceCode', function () {
                            return [
                                "uuid" => $this->priceCode->uuid,
                                'name' => $this->priceCode->name,
                                'created_at' => $this->priceCode->created_at,
                            ];

                            // return $this->prices->map(function ($prices) {
                            //     return [
                            //         "uuid" => $prices->uuid,
                            //         'name' => $prices->name,
                            //         'created_at' => $prices->created_at,
                            //     ];
                            // });
                        }),


                        "price" => $prices->price,
                        "discount_type" => $prices->discount_type,
                        "discount_value" => $prices->discount_value,
                        "discount_value_amount" => $prices->discount_value_amount,
                        "price_after_discount" => $prices->price_after_discount,
                        "pv" => $prices->pv,
                        "xv" => $prices->xv,
                        "bv" => $prices->bv,
                        "rv" => $prices->rv,
                    ];
                });
            }),
            "created_by" => $this->created_by,
            "created_at" => $this->created_at,
            "updated_by" => $this->updated_by,
            "updated_at" => $this->updated_at,
            "deleted_by" => $this->deleted_by,
            "deleted_at" => $this->deleted_at,
            // "product_category" => new ProductCategoryResource($this->when($this->relationLoaded('category'), $this->category)),
            'product_category' => $this->whenLoaded('category', function () {
                return [
                    "uuid" => $this->category->uuid,
                    "code" => $this->category->code,
                    "name" => $this->category->name,
                    "description" => $this->category->description,
                ];
            }),
        ];
    }
}
