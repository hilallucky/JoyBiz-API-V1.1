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
        Schema::create('week_periods', function (Blueprint $table) {
            $table->increments('id');
            $table->date('sDate');
            $table->string('sDay_name');
            $table->date('eDate');
            $table->string('eDay_name');
            $table->integer('interval_days')->default(0);
            $table->string('name')->nullable();
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
        Schema::dropIfExists('week_periodes');
    }
};
