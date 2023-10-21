<?php

namespace Database\Seeders;

use App\Models\Configs\PaymentType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentTypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    PaymentType::truncate();

    $csvFile = fopen(base_path("other-files/dump/master/payment_types.csv"), "r");

    $firstline = true;
    while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
      if (!$firstline) {
        PaymentType::create([
          "uuid" => $data['1'] ? $data['1'] : Str::uuid(),
          "ref_uuid" => $data['2'] ? $data['2'] : null,
          "code" => $data['3'] ? $data['3'] : null,
          "name" => $data['4'] ? $data['4'] : null,
          "description" => $data['5'] ? $data['5'] : 0,
          "charge_percent" => $data['6'] ? $data['6'] : 0,
          "charge_amount" => $data['7'] ? $data['7'] : 1,
          "effect" => $data['8'] ? $data['8'] : 1,
          "status_web" => $data['9'] ? $data['9'] : 1,
          "status" => $data['10'] ? $data['10'] : 0.4,
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
