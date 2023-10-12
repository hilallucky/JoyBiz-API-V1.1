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
        Schema::table('members', function (Blueprint $table) {
            $table->integer('min_bv')->default(0)->comment('Minimum required BV');
            $table->date('activated_at')->comment('Membership activate date')->nullable();
            $table->string('activated_by')->comment('Activated By (User ID from table user')->nullable();
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
            $table->dropColumn(['min_bv', 'activated_at', 'activated_by']);
        });
    }
};
