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
        Schema::table('members', function (Blueprint $table) {
            // $table->string('sponsor_uuid')->nullable()->comment('Direct Sponsor uuid')->after('sponsor_id');
            // $table->integer('placement_id')->nullable()->comment('Upline id')->after('sponsor_uuid');
            // $table->string('placement_uuid')->nullable()->comment('Upline uuid')->after('placement_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['sponsor_uuid']);
            $table->dropColumn(['placement_id']);
            $table->dropColumn(['placement_uuid']);
        });
    }
};
