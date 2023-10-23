<?php

namespace Database\Seeders;

use App\Models\Products\ProductCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {


    ProductCategory::truncate();

    $csvFile = fopen(base_path("other-files/dump/master/product_categories.csv"), "r");

    $firstline = true;
    while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
      if (!$firstline) {
        ProductCategory::create([
          "uuid" => $data['1'], //Str::uuid(),
          "name" => isset($data['2']) ? $data['2'] : null,
          "description" => isset($data['3']) ? $data['3'] : null,
          "status" => isset($data['4']) ? $data['4'] : 1,
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
