<?php

namespace Database\Seeders;

use App\Models\Products\ProductCategory;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class ProductCategorySeeder extends Seeder
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

        DB::table('product_categories')->insert([
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Electronics",
                "description" => "Electronics Desc",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Clothing",
                "description" => "Clothing Desc",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "name" => "Books",
                "description" => "Books Desc",
                "status" => "1",
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "created_by" => 'admin',
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);
    }
}
