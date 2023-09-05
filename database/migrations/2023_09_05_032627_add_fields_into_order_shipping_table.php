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
        Schema::table('order_shipping_temp', function (Blueprint $table) {
            $table->uuid('member_address_uuid')->comment('Get from table member_shipping_addresses')
                ->nullable()->after('discount_shipping_charge');
            $table->string('province')->comment('Province Name')->after('member_address_uuid');
            $table->string('city')->comment('City Name')->after('province');
            $table->string('district')->comment('District Name')->after('city');
            $table->string('village')->comment('Village Name')->after('district');
            $table->string('details')->comment('Address Detail')->after('village');
            $table->string('notes')->comment('Address Notes')->after('details');
            $table->string('remarks')->comment('Address remarks')->after('notes');
        });

        Schema::table('order_shipping', function (Blueprint $table) {
            $table->uuid('member_address_uuid')->comment('Get from table member_shipping_addresses')
                ->nullable()->after('discount_shipping_charge');
            $table->string('province')->comment('Province Name')->after('member_address_uuid');
            $table->string('city')->comment('City Name')->after('province');
            $table->string('district')->comment('District Name')->after('city');
            $table->string('village')->comment('Village Name')->after('district');
            $table->string('details')->comment('Address Detail')->after('village');
            $table->string('notes')->comment('Address Notes')->after('details');
            $table->string('remarks')->comment('Address remarks')->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_shipping_temp', function (Blueprint $table) {
            $table->dropColumn(['member_address_uuid']);
            $table->dropColumn(['province']);
            $table->dropColumn(['city']);
            $table->dropColumn(['district']);
            $table->dropColumn(['village']);
            $table->dropColumn(['details']);
            $table->dropColumn(['notes']);
            $table->dropColumn(['remarks']);
        });

        Schema::table('order_shipping', function (Blueprint $table) {
            $table->dropColumn(['member_address_uuid']);
            $table->dropColumn(['province']);
            $table->dropColumn(['city']);
            $table->dropColumn(['district']);
            $table->dropColumn(['village']);
            $table->dropColumn(['details']);
            $table->dropColumn(['notes']);
            $table->dropColumn(['remarks']);
        });
    }
};
