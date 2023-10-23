<?php

namespace Database\Seeders;

use App\Models\Products\ProductPrice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductPriceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    ProductPrice::truncate();

    $csvFile = fopen(base_path("other-files/dump/master/product_prices.csv"), "r");

    $firstline = true;
    while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
      if (!$firstline) {
        ProductPrice::create([
          "uuid" => $data['1'] ? $data['1'] : Str::uuid(),
          "product_uuid" => $data['2'] ? $data['2'] : null,
          "price_code_uuid" => $data['3'] ? $data['3'] : null,
          "price" => $data['4'] ? $data['4'] : 0,
          "status" => $data['5'] ? $data['5'] : 1,
          "discount_type" => $data['6'] ? $data['6'] : 'amount',
          "discount_value" => $data['7'] ? $data['7'] : 0,
          "discount_value_amount" => $data['8'] ? $data['8'] : 0,
          "price_after_discount" => $data['9'] ? $data['9'] : $data['4'],
          "cashback" => $data['10'] ? $data['10'] : 0,
          "cashback_reseller" => $data['11'] ? $data['11'] : 0,
          "shipping_budget" => $data['12'] ? $data['12'] : 0,
          "pv" => $data['13'] ? $data['13'] : 0,
          "xv" => $data['14'] ? $data['14'] : 0,
          "bv" => $data['15'] ? $data['15'] : 0,
          "rv" => $data['16'] ? $data['16'] : 0,
          "remarks" => null,
          'created_at' => Carbon::now(),
          'created_by' => 'admin',
        ]);
      }
      $firstline = false;
    }

    fclose($csvFile);
  }
}
