<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->call([
      // MovieSeeder::class,
      UserSeeder::class,
      MemberSeeder::class,
      CountrySeeder::class,
      ClientSeeder::class,
      ProductCategorySeeder::class,
      PriceCodeSeeder::class,
      ProductSeeder::class,
      ProductAttributeSeeder::class,
      CourierSeeder::class,
      PaymentTypeSeeder::class,
      RankSeeder::class,
      CitySeeder::class,
    ]);
  }
}
