<?php

namespace App\Http\Controllers\WMS;

use App\Http\Controllers\Controller;
use App\Repositories\WMS\DORepository;
use App\Services\WMS\DOService;
use Illuminate\Http\Request;

class DOController extends Controller
{
  private DOService $doService;

  public function __construct(DOService $doService)
  {
    $this->doService = $doService;
  }

  public function index(Request $request)
  {
    return $this->doService->index($request);
  }

  public function store(Request $request)
  {
    return $this->doService->store($request);
  }

  public function test(Request $request)
  {
    $array = [
      [
        "product_uuid" => "082efd04-4d49-11ee-9bbe-01601eb00f7f",
        "product_attribute_uuid" => null,
        "uuid" => "8b3da845-e54c-47e4-b808-dcf7857305bf",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "6ffb84f4-471f-4070-8aec-23e372de6c60",
        "name" => "Runaway",
        "remarks" => null,
        "notes" => null,
        "description" => "Runaway Desc",
        "total_weight" => 0.8,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 26,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => null,
        "uuid" => "121ce299-c606-4bd4-9be4-e38d60e9d39f",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "759d48a4-67c4-492f-9689-c07b2a42c721",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.8,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 4,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "6ee02ee2-1d85-4c80-8a56-907ec1175ac5",
        "uuid" => "ed2e9aac-21a0-4e29-bb9a-fe990f78a810",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "8fe0eff8-9598-448c-83d1-c8407a458138",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.4,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 2,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "6ee02ee2-1d85-4c80-8a56-907ec1175ac5",
        "uuid" => "cf13e328-28c1-464d-abd7-062373c64bax",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "5661df20-d02f-45b5-8d48-137049545d2e",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 4.0,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 20,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "6ee02ee2-1d85-4c80-8a56-907ec1175ac5",
        "uuid" => "cf13e328-28c1-464d-abd7-062373c64baf",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "5661df20-d02f-45b5-8d48-137049545d2e",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.4,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 2,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "458e5502-f0c7-46d1-83ac-a3671605a2e2",
        "uuid" => "da4dbddf-727b-459d-a06b-11c780d227bc",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "f911f19c-616e-4333-af7f-51fddd0e7faa",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.4,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 2,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "2f6a6bf3-ad7d-47c4-a74e-113cb40a7760",
        "uuid" => "e144581f-1865-4eaf-9a78-3d96cc43b647",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "6a2d52a4-cba1-44b9-9388-cd839986ad74",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.4,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 2,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ],
      [
        "product_uuid" => "080b6ed4-4d49-11ee-b49d-7d3eb780f12c",
        "product_attribute_uuid" => "6ee02ee2-1d85-4c80-8a56-907ec1175ac5",
        "uuid" => "c38d75a5-70bc-43ff-95a8-d930265cd078",
        "do_date" => "2023-11-09T04=>42=>34.623397Z",
        "warehouse_uuid" => null,
        "sent_to" => null,
        "to_uuid" => "41c60bf8-290e-46fa-82f1-6d3708689118",
        "name" => "Laptop",
        "remarks" => null,
        "notes" => null,
        "description" => "Laptop Desc",
        "total_weight" => 0.4,
        "stock_out" => null,
        "total_transaction" => null,
        "total_qty" => 2,
        "total_qty_sent" => null,
        "total_qty_indent" => 0,
        "total_qty_remain" => null,
        "stock_type" => "2"
      ]
    ];
    $repo = new DORepository;
    return $repo->groupArray($array);
  }
}
