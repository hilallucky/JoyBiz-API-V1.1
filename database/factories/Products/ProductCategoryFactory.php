<?php

namespace Database\Factories\Products;

use App\Models\Products\ProductCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        $nodeProvider = new RandomNodeProvider();

        return [
            [
            "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
            "name" => "Electronics",
            "description" => "Electronics Desc",
            "status" => 1,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            ],
            // [
            //     "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
            //     "name" => "Clothing",
            //     "description" => "Clothing Desc",
            //     "status" => 1,
            //     "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            //     "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            // ],
            // [
            //     "uuid" => Uuid::uuid1($nodeProvider->getNode())->toString(),
            //     "name" => "Books",
            //     "description" => "Books Desc",
            //     "status" => 1,
            //     "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            //     "updated_at" => Carbon::now()->format('Y-m-d H:i:s')
            // ],
        ];
    }
}
