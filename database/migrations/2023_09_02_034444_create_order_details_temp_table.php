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
        Schema::create('order_details_temp', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('order_header_temp_uuid')->comment('Get from table order_headers_temp');
            $table->uuid('product_price_uuid')->comment('Get from table product_prices');
            $table->integer('qty')->default(1);
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
            $table->enum('status', [0, 1, 2, 3, 4])->nullable()->comment('Status product : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated, 4 = Indent')->default(1);
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

            // $table->foreign('created_by')->references('uuid')->on('users');
            // $table->foreign('updated_by')->references('uuid')->on('users');
            // $table->foreign('deleted_by')->references('uuid')->on('users');

            // $table->foreign('order_header_temp_uuid')->references('uuid')->on('order_headers_temp')->onDelete('cascade');
            // $table->foreign('product_price_uuid')->references('uuid')->on('product_prices')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details_temp');
    }
};
