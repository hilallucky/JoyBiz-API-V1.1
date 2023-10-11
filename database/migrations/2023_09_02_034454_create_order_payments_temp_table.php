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
        Schema::create('order_payments_temp', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('payment_type_uuid')->comment('Get from table payment_types');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('total_discount', ['percentage', 'amount'])->nullable();
            $table->decimal('total_amount_after_discount', 10, 2)->default(0);
            $table->text('remarks')->comment('Notes of payment type')->nullable();
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

            $table->foreign('payment_type_uuid')->references('uuid')->on('payment_types')->onDelete('cascade');

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
        Schema::dropIfExists('order_payments_temp');
    }
};
