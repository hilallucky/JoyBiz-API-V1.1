<?php

namespace Database\Seeders;

use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nodeProvider = new RandomNodeProvider();

        $user_uuid = User::query()->first();
        $categories = ProductCategory::query()->get();

        foreach ($categories as $category) {
            switch ($category->name) {
                case 'Electronics':
                    $electronic_uuid = $category->uuid;
                    break;
                case 'Clothing':
                    $cloth_uuid = $category->uuid;
                    break;
                case 'Books':
                    $book_uuid = $category->uuid;
                    break;
            }
        }

        DB::table('products')->insert([
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "product_category_uuid" => $electronic_uuid,
                "name" => "Laptop",
                "description" => "Laptop Desc",
                "status" => 1,
                "created_by" => $user_uuid->uuid,
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "product_category_uuid" => $cloth_uuid,
                "name" => "T-Shirt",
                "description" => "T-Shirt Desc",
                "status" => 1,
                "created_by" => $user_uuid->uuid,
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "product_category_uuid" => $book_uuid,
                "name" => "Runaway",
                "description" => "Runaway Desc",
                "status" => 1,
                "created_by" => $user_uuid->uuid,
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);
    }
}
