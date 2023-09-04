<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_headers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('order_header_temp_uuid')->unique();
            $table->uuid('member_uuid')->comment('Get from table members');
            $table->uuid('price_code_uuid')->comment('Get from table price_codes');
            $table->text('remarks')->comment('Notes of product prices')->nullable();
            $table->decimal('total_discount_value', 10, 2)->nullable()->default(0);
            $table->decimal('total_discount_value_amount', 10, 2)->default(0);
            $table->decimal('total_price_after_discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('total_shipping_charge', 10, 2)->default(0);
            $table->decimal('total_payment_charge', 10, 2)->default(0);
            $table->decimal('total_amount_summary', 10, 2)->default(0);
            $table->decimal('total_pv', 10, 2)->default(0);
            $table->decimal('total_xv', 10, 2)->default(0);
            $table->decimal('total_bv', 10, 2)->default(0);
            $table->decimal('total_rv', 10, 2)->default(0);
            $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Pending, 1 = Paid, 2 = Posted, 3 = Rejected')->default(0);
            $table->string('airway_bill_no')->comment('AWB/Resi No')->nullable();
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

            // $table->foreign('created_by')->references('uuid')->on('users');
            // $table->foreign('updated_by')->references('uuid')->on('users');
            // $table->foreign('deleted_by')->references('uuid')->on('users');

            // $table->foreign('order_headers_temp_uuid')->references('uuid')->on('order_headers_temp')->onDelete('cascade');
            // // $table->foreign('user_uuid')->references('uuid')->on('users')->onDelete('cascade');
            // $table->foreign('member_uuid')->references('uuid')->on('members')->onDelete('cascade');
            // $table->foreign('price_code_uuid')->references('uuid')->on('price_codes')->onDelete('cascade');
            // // $table->foreign('shipping_uuid')->references('uuid')->on('couriers');

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
        Schema::dropIfExists('order_headers');
    }
};
