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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('first_name', 100)->comment('First Name');
            $table->string('last_name', 100)->comment('Last Name')->nullable();
            $table->integer('user_id')->unique(false)->nullable()->comment('User ID for login in users table');
            $table->string('phone', 100)->comment('Phone Number')->nullable();
            $table->integer('sponsor_id')->comment('Sponsor ID')->nullable();
            $table->integer('user_uuid')->comment('User uuid (get from table users)')->nullable();
            $table->uuid('country_id')->comment('Country ID (get from table countries')->nullable();
            $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')->default(1);
            $table->text('remarks')->comment('Notes of members')->nullable();
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
        Schema::dropIfExists('members');
    }
};
