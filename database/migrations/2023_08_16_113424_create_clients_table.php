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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('ip', 100)->comment('Client IP')->nullable();
            $table->string('domain_name', 100)->comment('Client Domaian Name')->nullable();
            $table->string('client_key')->comment('Client Key');
            $table->integer('status')->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated');
            $table->string('description')->comment('Description')->nullable();
            $table->string('phone', 100)->comment('Phone Number')->nullable();
            $table->uuid('country_id')->comment('Country ID (get from table countries')->nullable();
            $table->text('remarks')->comment('Notes of product code')->nullable();
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
        Schema::dropIfExists('clients');
    }
};
