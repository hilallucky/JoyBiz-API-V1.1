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
        // Table inventories
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->uuid('warehouse_uuid')->comment('Warehouse uuid based from table warehouses');
            $table->uuid('product_uuid')->comment('Product uuid based from table products');
            $table->uuid('purchase_uuid')->comment('Purchase uuid based from table purchases')->nullable();
            $table->integer('quantity');
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('warehouse_uuid')->references('uuid')->on('warehouses');
            $table->foreign('product_uuid')->references('uuid')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventories');
    }
};
