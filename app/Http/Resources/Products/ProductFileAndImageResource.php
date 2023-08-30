<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductFileAndImageResource extends JsonResource
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
            "product_uuid" => $this->product_uuid,
            "original_file_name" => $this->original_file_name,
            "file_name" => $this->file_name,
            "size" => $this->size,
            "type" => $this->type,
            "domain" => $this->domain,
            "path_file" => $this->path_file,
            "url" => $this->url,
            "status" => $this->status,
            // "created_by" => $this->created_by,
            // "created_at" => $this->created_at,
            // "updated_by" => $this->updated_by,
            // "updated_at" => $this->updated_at,
            // "deleted_by" => $this->deleted_by,
            // "deleted_at" => $this->deleted_at,
        ];
    }
}
