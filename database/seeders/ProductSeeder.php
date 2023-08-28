<?php

namespace Database\Seeders;

use App\Models\Products\PriceCode;
use App\Models\Products\Product;
use App\Models\Products\ProductAttribute;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductPrice;
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
        // $price_codes = ProductCategory::query()->get();

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

        // Price Code
        $productWIB = new PriceCode();
        $productWIB->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productWIB->code = "WIB";
        $productWIB->name = "Harga Indonesia Barat";
        $productWIB->description = "Harga Indonesia Barat Desc";
        $productWIB->status = 1;
        $productWIB->created_by = $user_uuid->uuid;
        $productWIB->save();

        $productWITA = new PriceCode();
        $productWITA->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productWITA->code = "WITA";
        $productWITA->name = "Harga Indonesia Tengah";
        $productWITA->description = "Harga Indonesia Tengah Desc";
        $productWITA->status = 1;
        $productWITA->created_by = $user_uuid->uuid;
        $productWITA->save();

        $productWIT = new PriceCode();
        $productWIT->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productWIT->code = "WIT";
        $productWIT->name = "Harga Indonesia Timur";
        $productWIT->description = "Harga Indonesia Timur Desc";
        $productWIT->status = 1;
        $productWIT->created_by = $user_uuid->uuid;
        $productWIT->save();

        // Product
        $product = new Product();
        $product->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $product->product_category_uuid = $electronic_uuid;
        $product->name = "Laptop";
        $product->description = "Laptop Desc";
        $product->status = 1;
        $product->created_by = $user_uuid->uuid;
        $product->save();

        $productPrice = new ProductPrice();
        $productPrice->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productPrice->product_uuid = $product->uuid;
        $productPrice->price_code_uuid = $productWIB->uuid;
        $productPrice->status = 1;
        $productPrice->price = 227000;
        $productPrice->discount_type = "amount";
        $productPrice->discount_value = 0;
        $productPrice->discount_value_amount = 0;
        $productPrice->price_after_discount = 227000;
        $productPrice->pv = 240;
        $productPrice->xv = 241;
        $productPrice->bv = 242;
        $productPrice->rv = 243;
        $productPrice->remarks = null;
        $productPrice->created_by = $user_uuid->uuid;
        $productPrice->save();

        $productPrice = new ProductPrice();
        $productPrice->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productPrice->product_uuid = $product->uuid;
        $productPrice->price_code_uuid = $productWITA->uuid;
        $productPrice->status = 1;
        $productPrice->price = 240000;
        $productPrice->discount_type = "amount";
        $productPrice->discount_value = 0;
        $productPrice->discount_value_amount = 0;
        $productPrice->price_after_discount = 240000;
        $productPrice->pv = 240;
        $productPrice->xv = 241;
        $productPrice->bv = 242;
        $productPrice->bv = 243;
        $productPrice->remarks = null;
        $productPrice->created_by = $user_uuid->uuid;
        $productPrice->save();

        $productPrice = new ProductPrice();
        $productPrice->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productPrice->product_uuid = $product->uuid;
        $productPrice->price_code_uuid = $productWIT->uuid;
        $productPrice->status = 1;
        $productPrice->price = 260000;
        $productPrice->discount_type = "amount";
        $productPrice->discount_value = 0;
        $productPrice->discount_value_amount = 0;
        $productPrice->price_after_discount = 260000;
        $productPrice->pv = 240;
        $productPrice->xv = 241;
        $productPrice->bv = 242;
        $productPrice->bv = 243;
        $productPrice->remarks = null;
        $productPrice->created_by = $user_uuid->uuid;
        $productPrice->save();


        $product = new Product();
        $product->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $product->product_category_uuid = $cloth_uuid;
        $product->name = "T-Shirt";
        $product->description = "T-Shirt Desc";
        $product->status = 1;
        $product->created_by = $user_uuid->uuid;
        $product->save();

        $productPrice = new ProductPrice();
        $productPrice->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productPrice->product_uuid = $product->uuid;
        $productPrice->price_code_uuid = $productWIB->uuid;
        $productPrice->status = 1;
        $productPrice->price = 127000;
        $productPrice->discount_type = "amount";
        $productPrice->discount_value = 0;
        $productPrice->discount_value_amount = 0;
        $productPrice->price_after_discount = 127000;
        $productPrice->pv = 240;
        $productPrice->xv = 241;
        $productPrice->bv = 242;
        $productPrice->bv = 243;
        $productPrice->remarks = null;
        $productPrice->created_by = $user_uuid->uuid;
        $productPrice->save();

        $productPrice = new ProductPrice();
        $productPrice->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productPrice->product_uuid = $product->uuid;
        $productPrice->price_code_uuid = $productWITA->uuid;
        $productPrice->status = 1;
        $productPrice->price = 140000;
        $productPrice->discount_type = "amount";
        $productPrice->discount_value = 0;
        $productPrice->discount_value_amount = 0;
        $productPrice->price_after_discount = 140000;
        $productPrice->pv = 240;
        $productPrice->xv = 241;
        $productPrice->bv = 242;
        $productPrice->bv = 243;
        $productPrice->remarks = null;
        $productPrice->created_by = $user_uuid->uuid;
        $productPrice->save();

        $productPrice = new ProductPrice();
        $productPrice->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productPrice->product_uuid = $product->uuid;
        $productPrice->price_code_uuid = $productWIT->uuid;
        $productPrice->status = 1;
        $productPrice->price = 160000;
        $productPrice->discount_type = "amount";
        $productPrice->discount_value = 0;
        $productPrice->discount_value_amount = 0;
        $productPrice->price_after_discount = 160000;
        $productPrice->pv = 240;
        $productPrice->xv = 241;
        $productPrice->bv = 242;
        $productPrice->bv = 243;
        $productPrice->remarks = null;
        $productPrice->created_by = $user_uuid->uuid;
        $productPrice->save();

        $productAttributes = new ProductAttribute();
        $productAttributes->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productAttributes->product_uuid = $product->uuid;
        $productAttributes->name = "Size";
        $productAttributes->description = "S";
        $productAttributes->status = 1;
        $product->created_by = $user_uuid->uuid;
        $productAttributes->save();

        $productAttributes = new ProductAttribute();
        $productAttributes->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productAttributes->product_uuid = $product->uuid;
        $productAttributes->name = "Size";
        $productAttributes->description = "M";
        $productAttributes->status = 1;
        $product->created_by = $user_uuid->uuid;
        $productAttributes->save();

        $productAttributes = new ProductAttribute();
        $productAttributes->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productAttributes->product_uuid = $product->uuid;
        $productAttributes->name = "Size";
        $productAttributes->description = "L";
        $productAttributes->status = 1;
        $product->created_by = $user_uuid->uuid;
        $productAttributes->save();

        $productAttributes = new ProductAttribute();
        $productAttributes->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productAttributes->product_uuid = $product->uuid;
        $productAttributes->name = "Size";
        $productAttributes->description = "XL";
        $productAttributes->status = 1;
        $product->created_by = $user_uuid->uuid;
        $productAttributes->save();

        $productAttributes = new ProductAttribute();
        $productAttributes->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productAttributes->product_uuid = $product->uuid;
        $productAttributes->name = "Color";
        $productAttributes->description = "Red";
        $productAttributes->status = 1;
        $product->created_by = $user_uuid->uuid;
        $productAttributes->save();

        $productAttributes = new ProductAttribute();
        $productAttributes->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productAttributes->product_uuid = $product->uuid;
        $productAttributes->name = "Color";
        $productAttributes->description = "White";
        $productAttributes->status = 1;
        $product->created_by = $user_uuid->uuid;
        $productAttributes->save();


        $product = new Product();
        $product->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $product->product_category_uuid = $book_uuid;
        $product->name = "Runaway";
        $product->description = "Runaway Desc";
        $product->status = 1;
        $product->created_by = $user_uuid->uuid;
        $product->save();

        $productPrice = new ProductPrice();
        $productPrice->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productPrice->product_uuid = $product->uuid;
        $productPrice->price_code_uuid = $productWIB->uuid;
        $productPrice->status = 1;
        $productPrice->price = 150000;
        $productPrice->discount_type = "amount";
        $productPrice->discount_value = 0;
        $productPrice->discount_value_amount = 0;
        $productPrice->price_after_discount = 150000;
        $productPrice->pv = 240;
        $productPrice->xv = 241;
        $productPrice->bv = 242;
        $productPrice->bv = 243;
        $productPrice->remarks = null;
        $productPrice->created_by = $user_uuid->uuid;
        $productPrice->save();

        $productPrice = new ProductPrice();
        $productPrice->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productPrice->product_uuid = $product->uuid;
        $productPrice->price_code_uuid = $productWITA->uuid;
        $productPrice->status = 1;
        $productPrice->price = 160000;
        $productPrice->discount_type = "amount";
        $productPrice->discount_value = 0;
        $productPrice->discount_value_amount = 0;
        $productPrice->price_after_discount = 160000;
        $productPrice->pv = 240;
        $productPrice->xv = 241;
        $productPrice->bv = 242;
        $productPrice->bv = 243;
        $productPrice->remarks = null;
        $productPrice->created_by = $user_uuid->uuid;
        $productPrice->save();

        $productPrice = new ProductPrice();
        $productPrice->uuid = Uuid::uuid1($nodeProvider->getNode())->toString();
        $productPrice->product_uuid = $product->uuid;
        $productPrice->price_code_uuid = $productWIT->uuid;
        $productPrice->status = 1;
        $productPrice->price = 170000;
        $productPrice->discount_type = "amount";
        $productPrice->discount_value = 0;
        $productPrice->discount_value_amount = 0;
        $productPrice->price_after_discount = 170000;
        $productPrice->pv = 240;
        $productPrice->xv = 241;
        $productPrice->bv = 242;
        $productPrice->bv = 243;
        $productPrice->remarks = null;
        $productPrice->created_by = $user_uuid->uuid;
        $productPrice->save();

    }
}
