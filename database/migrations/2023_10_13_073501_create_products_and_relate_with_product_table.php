<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Table product_categories
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->comment('Name of product category');
            $table->string('description')->comment('Description of product category')->nullable();
            $table->enum('status', [0, 1, 2, 3])->nullable()->default(1)
                ->comment('Status product_categories : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated');
            $table->text('remarks')->comment('Notes of product categories')->nullable();
            $table->string('created_by')->comment('Created By product_categories (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By product_categories (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By product_categories (User ID from table user')->nullable();
            $table->foreign('deleted_by')->references('uuid')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });


        // Table products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('category_uuid')
                ->comment('Product category uuid (get from table product_categories');
            $table->string('name')->comment('Name of product');
            $table->text('description')->comment('Description of product')->nullable();
            $table->enum('is_product_group', [0, 1])
                ->comment('Status : 0 = Single, 1 = Group/Bundling/Package')
                ->default(0);
            $table->enum('is_register', [0, 1])
                ->comment('Type Product for register : 0 = Non Register Product, 1 = Regular (Non Register) Product')
                ->default(0);
            $table->enum('status', [0, 1, 2, 3, 4])
                ->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated, 4 = Indent')
                ->default(1);
            $table->boolean('show_status')->comment('Show Status : true = Not Show, false = Show')->default(true);
            $table->boolean('sc_show_status')->comment('SC Show Status : true = Not Show, false = Show')->default(true);
            $table->decimal('weight', 10, 2)->comment('Status : 0 = Not Show, 1 = Show')->default(1);
            $table->text('remarks')->comment('Notes of products')->nullable();
            $table->string('created_by')->comment('Created By products (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By products (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By products (User ID from table user')->nullable();
            $table->foreign('category_uuid')->references('uuid')->on('product_categories');
            $table->timestamps();
            $table->softDeletes();
        });

        // Table product_attributes (e.g : size, color, type, etc)
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('product_uuid')
                ->comment('Product uuid in product_attributes (get from table products')
                ->nullable();
            $table->string('name')->comment('Name of product attribute')->nullable();
            $table->string('description')->comment('Description of product attribute')->nullable();
            $table->enum('status', [0, 1, 2, 3])
                ->nullable()
                ->comment('Status product_attributes : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')
                ->default(1);
            $table->text('remarks')->comment('Notes of product attributes')->nullable();
            $table->string('created_by')->comment('Created By product_attributes (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By product_attributes (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By product_attributes (User ID from table user')->nullable();
            $table->foreign('product_uuid')->references('uuid')->on('products');
            $table->timestamps();
            $table->softDeletes();
        });

        // Table product_group_headers
        Schema::create('product_group_headers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('product_uuid')->comment('Product uuid (get from table products')->nullable();
            $table->string('name')->comment('Name of product category');
            $table->text('description')->comment('Description of product category')->nullable();
            $table->enum('status', [0, 1, 2, 3])
                ->nullable()
                ->comment('Status product_group_headers : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')
                ->default(1);
            $table->text('remarks')->comment('Notes of product group headers')->nullable();
            $table->string('created_by')
                ->comment('Created By product_group_headers (User ID from table user')
                ->nullable();
            $table->string('updated_by')
                ->comment('Updated By product_group_headers (User ID from table user')
                ->nullable();
            $table->uuid('deleted_by')
                ->comment('Deleted By product_group_headers (User ID from table user')
                ->nullable();
            $table->foreign('product_uuid')->references('uuid')->on('products')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });


        // Table product_group_compositions
        Schema::create('product_group_compositions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('product_group_header_uuid')
                ->comment('Product uuid (get from table product_group_headers')
                ->nullable();
            $table->uuid('product_uuid')->comment('Product uuid (get from table products')->nullable();
            $table->decimal('qty', 10, 2)->default(1);
            $table->enum('status', [0, 1, 2, 3])
                ->nullable()
                ->comment('Status product_group_compositions : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')
                ->default(1);
            $table->string('created_by')
                ->comment('Created By product_group_compositions (User ID from table user')
                ->nullable();
            $table->string('updated_by')
                ->comment('Updated By product_group_compositions (User ID from table user')
                ->nullable();
            $table->uuid('deleted_by')
                ->comment('Deleted By product_group_compositions (User ID from table user')
                ->nullable();
            $table->foreign('product_uuid')->references('uuid')->on('products')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });


        // Table price_codes
        Schema::create('price_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->comment('Short code of price code')->unique();
            $table->string('name')->comment('Name of price code');
            $table->text('description')->comment('Description of product code')->nullable();
            $table->enum('status', [0, 1, 2, 3])
                ->nullable()
                ->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')
                ->default(1);
            $table->text('remarks')->comment('Notes of price code')->nullable();
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


        // Table product_prices
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('product_uuid');
            $table->uuid('price_code_uuid');
            $table->enum('status', [0, 1, 2, 3])
                ->nullable()->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('discount_type', ['percentage', 'amount'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable()->default(0);
            $table->decimal('discount_value_amount', 10, 2)->default(0);
            $table->decimal('price_after_discount', 10, 2)->default(0);
            $table->decimal('cashback', 10, 2)->default(0);
            $table->decimal('cashback_reseller', 10, 2)->default(0);
            $table->decimal('shipping_budget', 10, 2)->default(0);
            $table->decimal('pv', 10, 2)->default(0);
            $table->decimal('xv', 10, 2)->default(0);
            $table->decimal('bv', 10, 2)->default(0);
            $table->decimal('rv', 10, 2)->default(0);
            $table->text('remarks')->comment('Notes of product prices')->nullable();
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
            $table->foreign('product_uuid')->references('uuid')->on('products')->onDelete('cascade');
            $table->foreign('price_code_uuid')->references('uuid')->on('price_codes')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('product_group_headers');
        Schema::dropIfExists('product_group_compositions');
        Schema::dropIfExists('price_codes');
        Schema::dropIfExists('product_prices');
    }
};
