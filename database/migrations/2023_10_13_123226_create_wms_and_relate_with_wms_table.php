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
    // Table clients
    Schema::create('wms_warehouses', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('upline_uuid')->comment('Warehouse Upline')->nullable();
      $table->string('code', 25)->unique();
      $table->string('name');
      $table->string('phone');
      $table->string('mobile_phone', 25);
      $table->string('email', 150)->nullable();
      $table->string('province')->comment('Province Name')->nullable();
      $table->string('city')->comment('City Name')->nullable();
      $table->string('district')->comment('District Name')->nullable();
      $table->string('village')->comment('Village Name')->nullable();
      $table->string('zip_code')->comment('Zip Code')->nullable();
      $table->string('details')->comment('Address Detail')->nullable();
      $table->text('description')->nullable();
      $table->string('notes')->nullable();
      $table->string('remarks')->nullable();
      $table->enum('warehouse_type', [1, 2, 4])->comment('Warehouse Type : 1 = HQ, 2 = Branch, 3 = PUC, 4 = MPUC')->default(1);
      $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')->default(1);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });

    // Table stock process
    Schema::create('wms_stock_processes', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->date('process_date');
      $table->uuid('process_by_uuid')->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });

    // Table stocks
    Schema::create('wms_stock_summary_headers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('stock_process_uuid')->nullable();
      $table->uuid('warehouse_uuid')->nullable();
      $table->date('stock_date');
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->string('weight', 150);
      $table->integer('stock_in')->default(0);
      $table->integer('stock_out')->default(0);
      $table->integer('stock_previous')->default(0);
      $table->integer('stock_current')->default(0);
      $table->integer('stock_to_sale')->default(0);
      $table->integer('indent')->default(0);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      $table->foreign('stock_process_uuid')->references('uuid')->on('wms_stock_processes')->onDelete('cascade');
    });

    Schema::create('wms_stock_summary_details', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('wms_stock_summary_header_uuid')->nullable();
      $table->uuid('stock_process_uuid')->nullable();
      $table->uuid('warehouse_uuid')->nullable();
      $table->date('stock_date');
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->string('weight', 150);
      $table->integer('stock_in')->default(0);
      $table->integer('stock_out')->default(0);
      $table->integer('stock_previous')->default(0);
      $table->integer('stock_current')->default(0);
      $table->integer('stock_to_sale')->default(0);
      $table->integer('indent')->default(0);
      $table->enum('stock_type', [1, 2])->comment('Status type : 1 = Stock In, ;2 = Stock Out')->default(1);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      $table->foreign('stock_process_uuid')->references('uuid')->on('wms_stock_processes')->onDelete('cascade');
    });

    // Table weekly stocks
    Schema::create('wms_stock_summary_weekly_headers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('stock_process_uuid')->nullable();
      $table->uuid('warehouse_uuid')->nullable();
      $table->date('stock_date');
      $table->date('date_from')->nullable();
      $table->date('date_to')->nullable();
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->string('weight', 150);
      $table->integer('stock_in')->default(0);
      $table->integer('stock_out')->default(0);
      $table->integer('stock_previous')->default(0);
      $table->integer('stock_current')->default(0);
      $table->integer('stock_to_sale')->default(0);
      $table->integer('indent')->default(0);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      $table->foreign('stock_process_uuid')->references('uuid')->on('wms_stock_processes')->onDelete('cascade');
    });

    // Table monthly stocks
    Schema::create('wms_stock_summary_monthly_headers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('stock_process_uuid')->nullable();
      $table->uuid('warehouse_uuid')->nullable();
      $table->date('stock_date');
      $table->date('date_from')->nullable();
      $table->date('date_to')->nullable();
      $table->integer('month_from')->nullable();
      $table->integer('month_to')->nullable();
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->string('weight', 150);
      $table->integer('stock_in')->default(0);
      $table->integer('stock_out')->default(0);
      $table->integer('stock_previous')->default(0);
      $table->integer('stock_current')->default(0);
      $table->integer('stock_to_sale')->default(0);
      $table->integer('indent')->default(0);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      $table->foreign('stock_process_uuid')->references('uuid')->on('wms_stock_processes')->onDelete('cascade');
    });

    // Table yearly stocks
    Schema::create('wms_stock_summary_yearly_headers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('stock_process_uuid')->nullable();
      $table->uuid('warehouse_uuid')->nullable();
      $table->date('stock_date');
      $table->date('date_from')->nullable();
      $table->date('date_to')->nullable();
      $table->integer('year_from')->nullable();
      $table->integer('year_to')->nullable();
      $table->date('month_from')->nullable();
      $table->date('month_to')->nullable();
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->string('weight', 150);
      $table->integer('stock_in')->default(0);
      $table->integer('stock_out')->default(0);
      $table->integer('stock_previous')->default(0);
      $table->integer('stock_current')->default(0);
      $table->integer('stock_to_sale')->default(0);
      $table->integer('indent')->default(0);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      $table->foreign('stock_process_uuid')->references('uuid')->on('wms_stock_processes')->onDelete('cascade');
    });

    // Table wms orders
    Schema::create('wms_orders', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->date('transaction_date');
      $table->uuid('transaction_header_uuid')->nullable(); // could be order_header_uuid or purchasing_order_uuid
      $table->uuid('transaction_detail_uuid')->nullable(); // could be order_header_uuid or purchasing_order_uuid
      $table->uuid('warehouse_uuid')->nullable();
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->string('weight', 150);
      $table->integer('stock_in')->default(0);
      $table->integer('stock_out')->default(0);
      $table->integer('indent')->default(0);
      $table->enum('stock_type', [1, 2])->comment('Status type : 1 = Stock In, ;2 = Stock Out')->default(1);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      $table->foreign('stock_process_uuid')->references('uuid')->on('wms_stock_processes')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('wms_warehouses');
    Schema::dropIfExists('wms_stock_processes');
    Schema::dropIfExists('wms_stock_summary_headers');
    Schema::dropIfExists('wms_stock_summary_details');
    Schema::dropIfExists('wms_stock_summary_weekly_headers');
    Schema::dropIfExists('wms_stock_summary_monthly_headers');
    Schema::dropIfExists('wms_stock_summary_yearly_headers');
    Schema::dropIfExists('wms_orders');
  }
};
