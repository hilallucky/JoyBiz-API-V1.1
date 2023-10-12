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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('product_uuid');
            $table->uuid('price_code_uuid');
            $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('discount_type', ['percentage', 'amount'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable()->default(0);
            $table->decimal('discount_value_amount', 10, 2)->default(0);
            $table->decimal('price_after_discount', 10, 2)->default(0);
            $table->decimal('cashback', 10, 2)->default(0);
            $table->decimal('cashback_reseller', 10, 2)->default(0);
            $table->decimal('pv', 10, 2)->default(0);
            $table->decimal('xv', 10, 2)->default(0);
            $table->decimal('bv', 10, 2)->default(0);
            $table->decimal('rv', 10, 2)->default(0);
            $table->text('remarks')->comment('Notes of product prices')->nullable();
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

            // $table->foreign('created_by')->references('uuid')->on('users');
            // $table->foreign('updated_by')->references('uuid')->on('users');
            // $table->foreign('deleted_by')->references('uuid')->on('users');

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
        Schema::dropIfExists('product_price');
    }
};
