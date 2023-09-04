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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('order_payments_temp_uuid');
            $table->uuid('order_header_uuid');
            $table->uuid('payment_type_uuid')->comment('Get from table payment_types');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('total_discount', 10, 2)->default(0);
            $table->decimal('total_amount_after_discount', 10, 2)->default(0);
            $table->text('remarks')->comment('Notes of payment type')->nullable();
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

            // $table->foreign('order_payments_temp_uuid')->references('uuid')->on('order_payments_temp')->onDelete('cascade');
            // $table->foreign('order_headers_uuid')->references('uuid')->on('order_headers')->onDelete('cascade');
            // $table->foreign('payment_type_uuid')->references('uuid')->on('payment_types')->onDelete('cascade');

            // $table->foreign('created_by')->references('uuid')->on('users');
            // $table->foreign('updated_by')->references('uuid')->on('users');
            // $table->foreign('deleted_by')->references('uuid')->on('users');

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
        Schema::dropIfExists('order_payments');
    }
};
