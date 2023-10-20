<?php

namespace Database\Seeders;

use App\Models\Configs\Country;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CountrySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    Country::truncate();

    $csvFile = fopen(base_path("other-files/dump/master/countries.csv"), "r");

    $firstline = true;
    while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
      if (!$firstline) {
        Country::create([
          "uuid" => $data['1'] ? $data['1'] : Str::uuid(),
          "name" => $data['2'] ? $data['2'] : null,
          "name_iso" => $data['3'] ? $data['3'] : null,
          "region_name" => $data['4'] ? $data['4'] : null,
          "sub_region_name" => $data['5'] ? $data['5'] : null,
          "intermediate_region_name" => $data['6'] ? $data['6'] : null,
          "capital_city" => $data['7'] ? $data['7'] : null,
          "tld" => $data['8'] ? $data['8'] : null,
          "languages" => $data['9'] ? $data['9'] : null,
          "geoname_id" => $data['10'] ? $data['10'] : null,
          "dial_prefix" => $data['11'] ? $data['11'] : null,
          "alpha_2_iso" => $data['12'] ? $data['12'] : null,
          "alpha_3_iso" => $data['13'] ? $data['13'] : null,
          "corrency_code_iso" => $data['14'] ? $data['14'] : null,
          "currency_minor_unit_iso" => $data['15'] ? $data['15'] : null,
          "status" => Str::lower($data['2']) == 'indonesia' ? 1 : 0,
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
