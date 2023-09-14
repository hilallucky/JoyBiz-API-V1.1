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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code', 25)->unique();
            $table->string('name');
            $table->string('phone');
            $table->string('mobile_phone', 25);
            $table->string('email', 150);
            $table->string('province')->comment('Province Name')->nullable();
            $table->string('city')->comment('City Name')->nullable();
            $table->string('district')->comment('District Name')->nullable();
            $table->string('village')->comment('Village Name')->nullable();
            $table->string('zip_code')->comment('Zip Code')->nullable();
            $table->string('details')->comment('Address Detail')->nullable();
            $table->text('description')->nullable();
            $table->string('notes')->nullable();
            $table->string('remarks')->nullable();
            $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')->default(1);
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
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
        Schema::dropIfExists('warehouses');
    }
};
