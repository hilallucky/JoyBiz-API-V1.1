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
            // "id" => $this->id,
            "uuid" => $this->uuid,
            "name" => $this->name,
            "description" => $this->description,
            "varian" => ProductAttributeResource::collection(
                $this->whenLoaded(
                    'attributes'
                )
            ),
            "is_register" => $this->is_register,
            "status" => $this->status,
            "show_status" => $this->show_status,
            "sc_show_status" => $this->sc_show_status,
            "weight" => $this->weight,
            "remarks" => $this->remarks,
            "prices" => ProductPriceResource::collection(
                $this->whenLoaded(
                    'prices'
                )
            ),
            'product_category' => $this->whenLoaded(
                'category',
                function () {
                    return [
                        "uuid" => $this->category->uuid,
                        "name" => $this->category->name,
                        "description" => $this->category->description,
                    ];
                }
            ),
            "is_product_group" => $this->is_product_group,
            "composition" => ProductCompositionResource::collection(
                $this->whenLoaded(
                    'composition_by_header'
                )
            ),
            "images" => $this->whenLoaded(
                'images',
                function () {
                    return $this->images->map(
                        function ($image) {
                            return [
                                "uuid" => $image->uuid,
                                "path_file" => $image->path_file,
                                "url" => $image->url,
                            ];
                        }
                    );


                }
            ),
            "created_by" => $this->created_by,
            "created_at" => $this->created_at,
            "updated_by" => $this->updated_by,
            "updated_at" => $this->updated_at,
            "deleted_by" => $this->deleted_by,
            "deleted_at" => $this->deleted_at,
        ];
    }
}
