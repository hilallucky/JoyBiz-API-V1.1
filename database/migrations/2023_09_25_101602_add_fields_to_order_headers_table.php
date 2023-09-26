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
        Schema::table('order_headers', function (Blueprint $table) {
            $table->uuid('calculation_point_process_uuid')->nullable()->comment('Fill this field, if transaction already process to table calculation_point_members');
            $table->date('calculation_date')->nullable()->comment('Calculation date')->after('sponsor_id');;

            // $table->foreign('calculation_point_process_uuid')->references('process_uuid')->on('calculation_point_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_headers', function (Blueprint $table) {
            $table->dropColumn(['calculation_point_process_uuid']);
            $table->dropColumn(['calculation_date']);

            $table->dropForeign(['calculation_point_process_uuid']);
        });
    }
};
