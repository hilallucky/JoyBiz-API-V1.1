<?php

namespace Database\Seeders;

use App\Models\Configs\City;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    City::truncate();

    $csvFile = fopen(base_path("other-files/dump/master/cities.csv"), "r");

    $firstline = true;
    while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
      if (!$firstline) {
        City::create([
          "uuid" => $data['1'] ? $data['1'] : Str::uuid(),
          "country_uuid" => $data['2'] ? $data['2'] : null,
          "price_code" => $data['3'] ? $data['3'] : null,
          "price_code_uuid" =>$data['4'] ? $data['4'] : null,
          "area_code" => $data['5'] ? $data['5'] : null,
          "zip_code" => $data['6'] ? $data['6'] : null,
          "province" => $data['7'] ? $data['7'] : null,
          "city" => $data['8'] ? $data['8'] : null,
          "district" => $data['9'] ? $data['9'] : null,
          "village" => $data['10'] ? $data['10'] : null,
          "latitude" => $data['11'] ? $data['11'] : null,
          "longitude" => $data['12'] ? $data['12'] : null,
          "elevation" => $data['13'] ? $data['13'] : null,
          "status" => $data['14'] ? $data['14'] : 1,
          'created_at' => Carbon::now(),
          'created_by' => 'admin',

        ]);
      }
      $firstline = false;
    }

    fclose($csvFile);
  }
}
