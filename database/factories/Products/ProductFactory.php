<?php

namespace Database\Factories\Products;

use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $nodeProvider = new RandomNodeProvider();

        $user_uuid = User::query()->first();
        $electronic_uuid = ProductCategory::where(['name' => 'Electronics'])->query()->first();
        $cloth_uuid = ProductCategory::where(['name' => 'Clothing'])->query()->first();
        $book_uuid = ProductCategory::where(['name' => 'Books'])->query()->first();

        return [
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "category_uuid" => $electronic_uuid,
                "name" => "Laptop",
                "description" => "Laptop Desc",
                "status" => 1,
                "created_by" => $user_uuid,
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "category_uuid" => $cloth_uuid,
                "name" => "Laptop",
                "description" => "Laptop Desc",
                "status" => 1,
                "created_by" => $user_uuid,
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
                "category_uuid" => $book_uuid,
                "name" => "Laptop",
                "description" => "Laptop Desc",
                "status" => 1,
                "created_by" => $user_uuid,
                "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
                "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ];
    }
}
