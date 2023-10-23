<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCompositionResource extends JsonResource
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
            "product_group_header_uuid" => $this->product_group_header_uuid,
            'product' => $this->whenLoaded(
                'product_source',
                function () {
                    return [
                        'uuid' => $this->product_source->uuid,
                        'name' => $this->product_source->name,
                        'desc' => $this->product_source->desc,
                    ];
                }
            ),
            'qty' => $this->qty,
            //     // "created_by" => $this->created_by,
            //     // "created_at" => $this->created_at,
            //     // "updated_by" => $this->updated_by,
            //     // "updated_at" => $this->updated_at,
            //     // "deleted_by" => $this->deleted_by,
            //     // "deleted_at" => $this->deleted_at,
        ];

    }
}
