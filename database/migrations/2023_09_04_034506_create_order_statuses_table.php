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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('order_header_uuid')->unique();
            $table->enum('status', [0, 1, 2, 3, 4, 5, 6, 7])->nullable()->comment('Status : 0 = Pending, 1 = Paid, 2 = Posted, 3 = Rejected')->default(0);
            $table->uuid('reference_uuid')->comment('Get from table order header/detail/payment temporary or production');
            $table->string('description')->nullable();
            $table->text('remarks')->comment('Notes')->nullable();
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

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
        Schema::dropIfExists('order_statuses');
    }
};
