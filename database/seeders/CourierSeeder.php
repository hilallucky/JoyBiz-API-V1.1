<?php

namespace Database\Seeders;

use App\Models\Configs\Courier;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nodeProvider = new RandomNodeProvider();

        DB::table('couriers')->insert([
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "gallery_uuid" => null,
                "code" => "JNE",
                "name" => "JNE",
                "short_name" => "JNE",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "gallery_uuid" => null,
                "code" => "J&T",
                "name" => "J&T",
                "short_name" => "J&T",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "gallery_uuid" => null,
                "code" => "Sicepat",
                "name" => "Sicepat",
                "short_name" => "Sicepat",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "gallery_uuid" => null,
                "code" => "POS",
                "name" => "PT. POS Indonesia",
                "short_name" => "POS",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "gallery_uuid" => null,
                "code" => "Gosend",
                "name" => "Gosend",
                "short_name" => "Gosend",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);
    }
}
