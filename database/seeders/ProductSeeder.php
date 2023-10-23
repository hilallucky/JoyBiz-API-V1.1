<?php

namespace Database\Seeders;

use App\Models\Products\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    Product::truncate();

    $csvFile = fopen(base_path("other-files/dump/master/products.csv"), "r");

    $firstline = true;
    while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
      if (!$firstline) {
        Product::create([
          "uuid" => $data['1'] ? $data['1'] : Str::uuid(),
          "category_uuid" => $data['2'] ? $data['2'] : null,
          "name" => $data['3'] ? $data['3'] : null,
          "description" => $data['4'] ? $data['4'] : null,
          "is_product_group" => $data['5'] ? $data['5'] : 0,
          "is_register" => $data['6'] ? $data['6'] : 0,
          "status" => $data['7'] ? $data['7'] : 1,
          "show_status" => $data['8'] ? $data['8'] : 1,
          "sc_show_status" => $data['9'] ? $data['9'] : 1,
          "weight" => $data['10'] ? $data['10'] : 0.4,
          "remarks" => null,
          'created_at' => Carbon::now(),
          'created_by' => '02ff17f6-376f-49f8-adf7-3550d41ca884',

        ]);
      }
      $firstline = false;
    }

    fclose($csvFile);
  }
}
