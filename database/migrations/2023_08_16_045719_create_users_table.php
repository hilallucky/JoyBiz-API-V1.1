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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('first_name', 100)->description('Member first name');
            $table->string('last_name', 100)->description('Member last name')->nullable();
            $table->string('email')->unique()->description('Member email')->nullable();
            $table->dateTime('email_verified_at')->nullable()->description('Member email verification date');
            $table->string('password')->description('Member password');
            $table->timestamp('last_logged_in')->nullable()->description('Member latest login');
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
        Schema::dropIfExists('users');
    }
};
