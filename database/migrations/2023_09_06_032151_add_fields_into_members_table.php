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
            $table->string('sponsor_uuid')->nullable()->comment('Direct Sponsor uuid')->after('sponsor_id');
            $table->integer('upline_id')->nullable()->comment('Upline id')->after('sponsor_uuid');
            $table->string('upline_uuid')->nullable()->comment('Upline uuid')->after('upline_id');
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
            $table->dropColumn(['upline_id']);
            $table->dropColumn(['upline_uuid']);
        });
    }
};
