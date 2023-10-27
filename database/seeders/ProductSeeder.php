<?php

namespace Database\Seeders;

use App\Models\Products\PriceCode;
use App\Models\Products\Product;
use App\Models\Products\ProductAttribute;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductPrice;
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
    // Start Process Table Product Categories
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
    // End Process Table Product Categories

    // Start Process Table Products
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
          "allow_from_rank" => $data['11'] ? $data['11'] :  null,
          "allow_from_rank_id" => $data['12'] ? $data['12'] : 0,
          "allow_to_rank" => $data['13'] ? $data['13'] :  null,
          "allow_to_rank_id" => $data['14'] ? $data['14'] : 15,
          "remarks" => null,
          'created_at' => Carbon::now(),
          'created_by' => '02ff17f6-376f-49f8-adf7-3550d41ca884',

        ]);
      }
      $firstline = false;
    }

    fclose($csvFile);
    // End Process Table Products

    // Start Process Table Product Attributes

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
    // End Process Table Product Attributes

    // Start Process Table Product Group Headers

    // End Process Table Product Group Headers

    // Start Process Table Product Group Compositions

    // End Process Table Product Group Compositions

    // Start Process Table Price Codes
    PriceCode::truncate();

    $csvFile = fopen(base_path("other-files/dump/master/price_codes.csv"), "r");

    $firstline = true;
    while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
      if (!$firstline) {
        PriceCode::create([
          "uuid" => $data['1'] ? $data['1'] : Str::uuid(),
          "code" => $data['2'] ? $data['2'] : null,
          "name" => $data['3'] ? $data['3'] : null,
          "description" => $data['4'] ? $data['4'] : null,
          "status" => $data['5'] ? $data['5'] : null,
          "remarks" => null,
          'created_at' => Carbon::now(),
          'created_by' => 'admin',

        ]);
      }
      $firstline = false;
    }

    fclose($csvFile);
    // End Process Table Price Codes

    // Start Process Table Product Prices
    ProductPrice::truncate();

    $csvFile = fopen(base_path("other-files/dump/master/product_prices.csv"), "r");

    $firstline = true;
    while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
      if (!$firstline) {
        ProductPrice::create([
          "uuid" => $data['1'] ? $data['1'] : Str::uuid(),
          "product_uuid" => $data['2'] ? $data['2'] : null,
          "price_code_uuid" => $data['3'] ? $data['3'] : null,
          "price_member" => $data['4'] ? $data['4'] : 0,
          "status" => $data['5'] ? $data['5'] : 1,
          "discount_type" => $data['6'] ? $data['6'] : 'amount',
          "discount_value" => $data['7'] ? $data['7'] : 0,
          "discount_value_amount" => $data['8'] ? $data['8'] : 0,
          "price_member_after_discount" => $data['9'] ? $data['9'] : $data['4'],
          "cashback" => $data['10'] ? $data['10'] : 0,
          "cashback_reseller" => $data['11'] ? $data['11'] : 0,
          "shipping_budget" => $data['12'] ? $data['12'] : 0,
          "pv" => $data['13'] ? $data['13'] : 0,
          "xv" => $data['14'] ? $data['14'] : 0,
          "bv" => $data['15'] ? $data['15'] : 0,
          "rv" => $data['16'] ? $data['16'] : 0,
          "price_consument" => $data['17'] ? $data['17'] : 0,
          "remarks" => null,
          'created_at' => Carbon::now(),
          'created_by' => 'admin',
        ]);
      }
      $firstline = false;
    }

    fclose($csvFile);
    // End Process Table Product Prices
  }
}
