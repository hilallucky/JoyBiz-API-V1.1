<?php

namespace App\Http\Resources\WMS;

use Illuminate\Http\Resources\Json\JsonResource;

class GetTransactionResource extends JsonResource
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
          "get_date" => $this->get_date,
          "do_uuid" => $this->wms_do_header_uuid,
          "do_date" => $this->wms_do_date,
          "transaction_type" => $this->transaction_type,
          "transaction_date" => $this->transaction_date,
          "transaction_uuid" => $this->transaction_header_uuid,
          "transaction_detail_uuid" => $this->transaction_detail_uuid,
          "warehouse_uuid" => $this->warehouse_uuid,
          "product_uuid" => $this->product_uuid,
          "product_attribute_uuid" => $this->product_attribute_uuid,
          "product_header_uuid" => $this->product_header_uuid,
          "name" => $this->name,
          "attribute_name" => $this->attribute_name,
          "description" => $this->description,
          "is_register" => $this->is_register,
          "weight" => $this->weight,
          "stock_in" => $this->stock_in,
          "stock_out" => $this->stock_out,
          "qty" => $this->qty,
          "qty_indent" => $this->qty_indent,
          "product_status" => $this->product_status,
          "stock_type" => $this->stock_type,
          "created_by" => $this->created_by,
          "updated_by" => $this->updated_by,
          "deleted_by" => $this->deleted_by,
      ];
    }
}
