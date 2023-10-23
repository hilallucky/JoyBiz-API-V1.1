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
      // Table order_shipping_temp
      Schema::create('order_shipping_temp', function (Blueprint $table) {
        $table->id();
        $table->uuid('uuid')->unique();
        $table->uuid('order_header_temp_uuid')->comment('Get from table order_headers_temp');
        $table->uuid('courier_uuid')->comment('Get from table couriers');
        $table->decimal('shipping_charge', 10, 2)->default(0);
        $table->decimal('discount_shipping_charge', 10, 2)->default(0);
        $table->string('receiver_name')->comment('Receiver Name')->nullable();
        $table->string('receiver_phone_number')->comment('Receiver Phone Number')->nullable();
        $table->uuid('member_shipping_address_uuid')->comment('Get from table member_shipping_addresses')->nullable();
        $table->string('province')->comment('Province Name');
        $table->string('city')->comment('City Name');
        $table->string('district')->comment('District Name');
        $table->string('village')->comment('Village Name');
        $table->string('details')->comment('Address Detail');
        $table->string('notes')->comment('Address Notes')->nullable();
        $table->string('remarks')->comment('Address remarks')->nullable();
        $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
        $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
        $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
  
        $table->foreign('order_header_temp_uuid')->references('uuid')->on('order_headers_temp')->onDelete('cascade');
        $table->foreign('courier_uuid')->references('uuid')->on('couriers')->onDelete('cascade');
        $table->foreign('member_shipping_address_uuid')->references('uuid')->on('member_shipping_addresses')->onDelete('cascade');
  
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
