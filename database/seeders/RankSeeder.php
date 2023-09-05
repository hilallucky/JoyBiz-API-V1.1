<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class RankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nodeProvider = new RandomNodeProvider();

        DB::table('ranks')->insert([
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Civilian",
                "short_name" => "C",
                "description" => "Civilian",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Joykeeper",
                "short_name" => "JK",
                "description" => "Joykeeper",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Joypriser",
                "short_name" => "JPS",
                "description" => "Joypriser",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Joypreneur",
                "short_name" => "JP",
                "description" => "Joypreneur",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "JoyBizPreneur",
                "short_name" => "JBP",
                "description" => "JoyBizPreneur",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Baron JBpreneur",
                "short_name" => "BJ",
                "description" => "Baron JBpreneur",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Viscount JBpreneur",
                "short_name" => "VJ",
                "description" => "Viscount JBpreneur",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Earl JBpreneur",
                "short_name" => "EJ",
                "description" => "Earl JBpreneur",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Marquis JBpreneur",
                "short_name" => "MJ",
                "description" => "Marquis JBpreneur",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Duke JBpreneur",
                "short_name" => "DJ",
                "description" => "Duke JBpreneur",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Crown Ambassador",
                "short_name" => "CA",
                "description" => "Crown Ambassador",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Royal Crown Ambassador",
                "short_name" => "RCA",
                "description" => "Royal Crown Ambassador",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);
    }
}
