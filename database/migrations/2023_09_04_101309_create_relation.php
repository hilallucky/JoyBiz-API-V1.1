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
        Schema::table('order_headers_temp', function (Blueprint $table) {
            // $table->foreign('created_by')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreign('member_uuid')->references('uuid')->on('members')->onDelete('cascade');
            $table->foreign('price_code_uuid')->references('uuid')->on('price_codes')->onDelete('cascade');
        });

        Schema::table('order_details_temp', function (Blueprint $table) {
            $table->foreign('order_header_temp_uuid')->references('uuid')->on('order_headers_temp')->onDelete('cascade');
            $table->foreign('product_price_uuid')->references('uuid')->on('product_prices')->onDelete('cascade');
        });

        Schema::table('order_payments_temp', function (Blueprint $table) {
            $table->foreign('order_header_temp_uuid')->references('uuid')->on('order_headers_temp')->onDelete('cascade');
            $table->foreign('payment_type_uuid')->references('uuid')->on('payment_types')->onDelete('cascade');
        });

        Schema::table('order_shipping_temp', function (Blueprint $table) {
            $table->foreign('order_header_temp_uuid')->references('uuid')->on('order_headers_temp')->onDelete('cascade');
            $table->foreign('courier_uuid')->references('uuid')->on('couriers')->onDelete('cascade');
            $table->foreign('member_shipping_address_uuid')->references('uuid')->on('member_shipping_addresses')->onDelete('cascade');
        });

        Schema::table('order_headers', function (Blueprint $table) {
            $table->foreign('order_header_temp_uuid')->references('uuid')->on('order_headers_temp')->onDelete('cascade');
            // $table->foreign('created_by')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreign('member_uuid')->references('uuid')->on('members')->onDelete('cascade');
            $table->foreign('price_code_uuid')->references('uuid')->on('price_codes')->onDelete('cascade');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->foreign('order_details_temp_uuid')->references('uuid')->on('order_details_temp'); //->onDelete('cascade');
            $table->foreign('order_header_uuid')->references('uuid')->on('order_headers'); //->onDelete('cascade');
            $table->foreign('product_price_uuid')->references('uuid')->on('product_prices'); //->onDelete('cascade');
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->foreign('order_payments_temp_uuid')->references('uuid')->on('order_payments_temp')->onDelete('cascade');
            $table->foreign('order_header_uuid')->references('uuid')->on('order_headers')->onDelete('cascade');
            $table->foreign('payment_type_uuid')->references('uuid')->on('payment_types')->onDelete('cascade');
        });

        Schema::table('order_shipping', function (Blueprint $table) {
            $table->foreign('order_shipping_temp_uuid')->references('uuid')->on('order_shipping_temp')->onDelete('cascade');
            $table->foreign('order_header_uuid')->references('uuid')->on('order_headers')->onDelete('cascade');
            $table->foreign('courier_uuid')->references('uuid')->on('couriers')->onDelete('cascade');
            $table->foreign('member_shipping_address_uuid')->references('uuid')->on('member_shipping_addresses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_headers_temp', function (Blueprint $table) {
            $table->dropForeign(['user_uuid']);
            $table->dropForeign(['member_uuid']);
            $table->dropForeign(['price_code_uuid']);
        });

        Schema::table('order_details_temp', function (Blueprint $table) {
            $table->dropForeign(['order_header_temp_uuid']);
            $table->dropForeign(['product_price_uuid']);
        });

        Schema::table('order_payments_temp', function (Blueprint $table) {
            $table->dropForeign(['order_header_temp_uuid']);
            $table->dropForeign(['payment_type_uuid']);
        });

        Schema::table('order_shipping_temp', function (Blueprint $table) {
            $table->dropForeign(['order_header_temp_uuid']);
            $table->dropForeign(['courier_uuid']);
        });

        Schema::table('order_headers', function (Blueprint $table) {
            $table->dropForeign(['order_header_temp_uuid']);
            $table->dropForeign(['user_uuid']);
            $table->dropForeign(['member_uuid']);
            $table->dropForeign(['price_code_uuid']);
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['order_details_temp_uuid']);
            $table->dropForeign(['order_header_uuid']);
            $table->dropForeign(['product_price_uuid']);
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropForeign(['order_payments_temp_uuid']);
            $table->dropForeign(['order_header_uuid']);
            $table->dropForeign(['payment_type_uuid']);
        });

        Schema::table('order_shipping', function (Blueprint $table) {
            $table->dropForeign(['order_shipping_temp_uuid']);
            $table->dropForeign(['order_header_uuid']);
            $table->dropForeign(['courier_uuid']);
        });
    }
};
