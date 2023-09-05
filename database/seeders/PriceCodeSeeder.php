<?php

namespace Database\Seeders;

use App\Models\Products\PriceCode;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class PriceCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ProductCategory::factory()->create();
        $nodeProvider = new RandomNodeProvider();

        DB::table('price_codes')->insert([
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "code" => "WIB",
                "name" => "Harga Indonesia Barat",
                "description" => "Harga Indonesia Barat Desc",
                "status" => "1",
                "remarks" => null,
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "code" => "WITA",
                "name" => "Harga Indonesia Tengah",
                "description" => "Harga Indonesia Tengah Desc",
                "status" => "1",
                "remarks" => null,
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "code" => "WIT",
                "name" => "Harga Indonesia Timur",
                "description" => "Harga Indonesia Timur Desc",
                "status" => "1",
                "remarks" => null,
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);
    }
}
