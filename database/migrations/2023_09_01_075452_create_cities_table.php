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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('country_uuid');
            $table->string('area_code', 30);
            $table->string('zip_code', 10);
            $table->string('province', 100);
            $table->string('city', 100);
            $table->string('district', 100);
            $table->string('village', 30);
            $table->string('latitude', 10);
            $table->string('longitude', 100);
            $table->string('elevation', 100);
            $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')->default(1);
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
        Schema::dropIfExists('cities');
    }
};
