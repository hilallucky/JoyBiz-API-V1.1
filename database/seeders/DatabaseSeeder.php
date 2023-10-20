<?php

namespace Database\Seeders;

use App\Models\Members\Member;
use App\Models\Users\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

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
      RankSeeder::class,
      CitySeeder::class,
    ]);
  }
}
