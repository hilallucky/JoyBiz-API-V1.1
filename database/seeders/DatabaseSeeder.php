<?php

namespace Database\Seeders;

use App\Models\Members\Member;
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

        /* Create Top member */
        $member = new Member;
        $member->uuid = Uuid::uuid4()->toString();
        $member->first_name = 'Top Member';
        $member->last_name = '--';
        $member->sponsor_id = null;
        $member->sponsor_uuid = null;
        $member->placement_id = null;
        $member->placement_uuid = null;
        $member->phone = null;
        $member->status = 1;
        $member->save();

        /* create default user */
        User::create([
            "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
            'first_name' => $member->first_name, //'John',
            'last_name' => $member->last_name, //'Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('secret'),
            'validation_code' => Hash::make('secret'),
        ]);

        $this->call([
            MovieSeeder::class,
            CountrySeeder::class,
            ClientSeeder::class,
            ProductCategorySeeder::class,
            // PriceCodeSeeder::class,
            ProductSeeder::class,
            CourierSeeder::class,
            RankSeeder::class,
        ]);
    }
}
