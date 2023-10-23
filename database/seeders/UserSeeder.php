<?php

namespace Database\Seeders;

use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    User::truncate();

    $csvFile = fopen(base_path("other-files/dump/master/users.csv"), "r");

    $firstline = true;
    while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
      if (!$firstline) {
        User::create([
          "uuid" => isset($data['1']) ? $data['1'] : null,
          "first_name" => isset($data['2']) ? $data['2'] : null,
          "last_name" => isset($data['3']) ? $data['3'] : null,
          "email" => isset($data['4']) ? $data['4'] : null,
          "validation_code" => isset($data['5']) ? $data['5'] : null,
          "email_verified_at" => Carbon::now(),
          "password" => isset($data['7']) ? $data['7'] : null,
          "last_logged_in" => Carbon::now(),
          "status" => isset($data['9']) ? $data['9'] : 0,
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
