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
        Schema::create('order_shipping_temp', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('order_header_temp_uuid')->comment('Get from table order_headers_temp');
            $table->uuid('courier_uuid')->comment('Get from table couriers');
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('discount_shipping_charge', 10, 2)->default(0);
            $table->uuid('member_shipping_address_uuid')->comment('Get from table member_shipping_addresses')
                ->nullable()->after('discount_shipping_charge');
            $table->string('province')->comment('Province Name')->after('member_address_uuid');
            $table->string('city')->comment('City Name')->after('province');
            $table->string('district')->comment('District Name')->after('city');
            $table->string('village')->comment('Village Name')->after('district');
            $table->string('details')->comment('Address Detail')->after('village')->nullable();
            $table->string('notes')->comment('Address Notes')->after('details')->nullable();
            $table->string('remarks')->comment('Address remarks')->after('notes')->nullable();
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

            // $table->foreign('created_by')->references('uuid')->on('users');
            // $table->foreign('updated_by')->references('uuid')->on('users');
            // $table->foreign('deleted_by')->references('uuid')->on('users');

            // $table->foreign('order_header_temp_uuid')->references('uuid')->on('order_headers_temp')->onDelete('cascade');
            // $table->foreign('courier_uuid')->references('uuid')->on('couriers')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_shipping_temp');
    }
};
