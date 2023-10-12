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
        Schema::table('product_prices', function (Blueprint $table) {
            $table->decimal('cashback', 10, 2)->default(0);
            $table->decimal('cashback_reseller', 10, 2)->default(0);
        });
        Schema::table('order_details_temp', function (Blueprint $table) {
            $table->decimal('cashback', 10, 2)->default(0);
            $table->decimal('cashback_reseller', 10, 2)->default(0);
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->decimal('cashback', 10, 2)->default(0);
            $table->decimal('cashback_reseller', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropColumn(['cashback', 'cashback_reseller']);
        });
        Schema::table('order_details_temp', function (Blueprint $table) {
            $table->dropColumn(['cashback', 'cashback_reseller']);
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['cashback', 'cashback_reseller']);
        });
    }
};
