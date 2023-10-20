<?php

namespace Database\Seeders;

use App\Models\Products\ProductAttribute;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductAttributeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    ProductAttribute::truncate();

    $csvFile = fopen(base_path("other-files/dump/master/product_attributes.csv"), "r");

    $firstline = true;
    while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
      if (!$firstline) {
        ProductAttribute::create([
          "uuid" => $data['1'] ? $data['1'] : Str::uuid(),
          "product_uuid" => $data['2'] ? $data['2'] : null,
          "name" => $data['3'] ? $data['3'] : null,
          "description" => $data['4'] ? $data['4'] : null,
          "status" => $data['5'] ? $data['5'] : 1,
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
