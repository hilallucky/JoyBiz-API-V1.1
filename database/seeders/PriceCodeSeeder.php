<?php

namespace Database\Seeders;

use App\Models\Products\PriceCode;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PriceCodeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
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
  }
}
