<?php

namespace Database\Seeders;

use App\Models\Users\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // $this->call('UsersTableSeeder');

        $nodeProvider = new RandomNodeProvider();

        /* create default user */
        User::create([
            "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('secret'),
        ]);

        $this->call([
            MovieSeeder::class,
            CountrySeeder::class,
            ClientSeeder::class,
            ProductCategorySeeder::class,
            // PriceCodeSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
