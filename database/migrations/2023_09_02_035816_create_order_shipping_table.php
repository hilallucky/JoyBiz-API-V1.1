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
        Schema::create('order_shipping', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('order_shipping_temp_uuid');
            $table->uuid('order_header_uuid');
            $table->uuid('courier_uuid')->comment('Get from table couriers');
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('discount_shipping_charge', 10, 2)->default(0);
            $table->uuid('member_shipping_address_uuid')->comment('Get from table member_shipping_addresses')
                ->nullable()->after('discount_shipping_charge');
            $table->string('province')->comment('Province Name')->nullable();
            $table->string('city')->comment('City Name')->after('province')->nullable();
            $table->string('district')->comment('District Name')->after('city')->nullable();
            $table->string('village')->comment('Village Name')->after('district')->nullable();
            $table->string('details')->comment('Address Detail')->after('village')->nullable();
            $table->string('notes')->comment('Address Notes')->after('details')->nullable();
            $table->string('remarks')->comment('Address remarks')->after('notes')->nullable();
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

            // $table->foreign('order_shipping_temp_uuid')->references('uuid')->on('order_shipping_temp')->onDelete('cascade');
            // $table->foreign('order_header_uuid')->references('uuid')->on('order_headers')->onDelete('cascade');
            // $table->foreign('courier_uuid')->references('uuid')->on('couriers')->onDelete('cascade');

            // $table->foreign('created_by')->references('uuid')->on('users');
            // $table->foreign('updated_by')->references('uuid')->on('users');
            // $table->foreign('deleted_by')->references('uuid')->on('users');

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
        Schema::dropIfExists('order_shipping');
    }
};
