<?php

namespace Database\Seeders;

use App\Models\Members\Member;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MemberSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Member::truncate();

    $csvFile = fopen(base_path("other-files/dump/master/members.csv"), "r");

    $firstline = true;
    while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
      if (!$firstline) {
        Member::create([
          "uuid" => $data['1'] ? $data['1'] : Str::uuid(),
          "first_name" => $data['2'] ? $data['2'] : null,
          "last_name" => $data['3'] ? $data['3'] : null,
          "id_no" => $data['4'] ? $data['4'] : null,
          "phone" => $data['5'] ? $data['5'] : null,
          "placement_id" => $data['6'] ? $data['6'] : null,
          "placement_uuid" => $data['7'] ? $data['7'] : null,
          "sponsor_id" => $data['8'] ? $data['8'] : null,
          "sponsor_uuid" => $data['9'] ? $data['9'] : null,
          "user_id" => $data['10'] ? $data['10'] : null,
          "user_uuid" => $data['11'] ? $data['11'] : null,
          "country_uuid" => $data['12'] ? $data['12'] : null,
          "membership_status" => $data['13'] ? $data['13'] : 0,
          "status" => $data['14'] ? $data['14'] : 0,
          "will_dormant_at" => $data['15'] ? $data['15'] : null,
          "min_bv" => $data['16'] ? $data['16'] : 0,
          "remarks" => null,
          'activated_at' => Carbon::now(),
          'created_at' => Carbon::now(),
          'created_by' => 'admin',

        ]);
      }
      $firstline = false;
    }

    fclose($csvFile);
  }
}
