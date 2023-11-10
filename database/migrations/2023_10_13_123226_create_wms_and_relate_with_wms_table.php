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
      $table->timestamp('processed_date');
      $table->uuid('processed_by_uuid')->nullable();
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
      $table->integer('stock_process_uuid')->nullable();
      $table->uuid('warehouse_uuid')->nullable();
      $table->timestamp('stock_date');
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->decimal('weight', 10, 2)->comment('Product weight')->default(0.3)->nullable();
      $table->integer('stock_in')->default(0)->nullable();
      $table->integer('stock_out')->default(0)->nullable();
      $table->integer('stock_previous')->default(0)->nullable();
      $table->integer('stock_current')->default(0)->nullable();
      $table->integer('stock_to_sale')->default(0)->nullable();
      $table->integer('indent')->default(0)->nullable();
      $table->enum('stock_type', [1, 2])->comment('Status type : 1 = Stock In, 2 = Stock Out')->default(1);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      //$table->foreign('stock_process_uuid')->references('id')->on('wms_stock_processes')->onDelete('cascade');
    });

    Schema::create('wms_stock_summary_details', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('wms_stock_summary_header_uuid')->nullable();
      $table->integer('stock_process_uuid')->nullable();
      $table->uuid('warehouse_uuid')->nullable();
      $table->timestamp('stock_date');
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->decimal('weight', 10, 2)->comment('Product weight')->default(0.3)->nullable();
      $table->integer('stock_in')->default(0)->nullable();
      $table->integer('stock_out')->default(0)->nullable();
      $table->integer('stock_previous')->default(0)->nullable();
      $table->integer('stock_current')->default(0)->nullable();
      $table->integer('stock_to_sale')->default(0)->nullable();
      $table->integer('indent')->default(0)->nullable();
      $table->enum('stock_type', [1, 2])->comment('Status type : 1 = Stock In, 2 = Stock Out')->default(1);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      //$table->foreign('stock_process_uuid')->references('id')->on('wms_stock_processes')->onDelete('cascade');
    });

    // Table weekly stocks
    Schema::create('wms_stock_summary_weekly_headers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->integer('stock_process_uuid')->nullable();
      $table->uuid('warehouse_uuid')->nullable();
      $table->timestamp('stock_date');
      $table->timestamp('date_from')->nullable();
      $table->timestamp('date_to')->nullable();
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->decimal('weight', 10, 2)->comment('Product weight')->default(0.3)->nullable();
      $table->integer('stock_in')->default(0)->nullable();
      $table->integer('stock_out')->default(0)->nullable();
      $table->integer('stock_previous')->default(0)->nullable();
      $table->integer('stock_current')->default(0)->nullable();
      $table->integer('stock_to_sale')->default(0)->nullable();
      $table->integer('indent')->default(0)->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      //$table->foreign('stock_process_uuid')->references('id')->on('wms_stock_processes')->onDelete('cascade');
    });

    // Table monthly stocks
    Schema::create('wms_stock_summary_monthly_headers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->integer('stock_process_uuid')->nullable();
      $table->uuid('warehouse_uuid')->nullable();
      $table->timestamp('stock_date');
      $table->timestamp('date_from')->nullable();
      $table->timestamp('date_to')->nullable();
      $table->integer('month_from')->nullable();
      $table->integer('month_to')->nullable();
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->decimal('weight', 10, 2)->comment('Product weight')->default(0.3)->nullable();
      $table->integer('stock_in')->default(0)->nullable();
      $table->integer('stock_out')->default(0)->nullable();
      $table->integer('stock_previous')->default(0)->nullable();
      $table->integer('stock_current')->default(0)->nullable();
      $table->integer('stock_to_sale')->default(0)->nullable();
      $table->integer('indent')->default(0)->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      //$table->foreign('stock_process_uuid')->references('id')->on('wms_stock_processes')->onDelete('cascade');
    });

    // Table yearly stocks
    Schema::create('wms_stock_summary_yearly_headers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->integer('stock_process_uuid')->nullable();
      $table->uuid('warehouse_uuid')->nullable();
      $table->timestamp('stock_date');
      $table->timestamp('date_from')->nullable();
      $table->timestamp('date_to')->nullable();
      $table->integer('year_from')->nullable();
      $table->integer('year_to')->nullable();
      $table->timestamp('month_from')->nullable();
      $table->timestamp('month_to')->nullable();
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->decimal('weight', 10, 2)->comment('Product weight')->default(0.3)->nullable();
      $table->integer('stock_in')->default(0)->nullable();
      $table->integer('stock_out')->default(0)->nullable();
      $table->integer('stock_previous')->default(0)->nullable();
      $table->integer('stock_current')->default(0)->nullable();
      $table->integer('stock_to_sale')->default(0)->nullable();
      $table->integer('indent')->default(0)->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      //$table->foreign('stock_process_uuid')->references('id')->on('wms_stock_processes')->onDelete('cascade');
    });

    // Table wms_transactions
    Schema::create('wms_get_transactions', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->timestamp('get_date', 0);
      $table->uuid('wms_do_header_uuid')->nullable();
      $table->timestamp('wms_do_date')->nullable();
      $table->enum('transaction_type', [1, 2, 3, 4, 5])->comment('Transaction type : 1 = Sales, 2 = PO')->default(1);
      $table->timestamp('transaction_date', 0);
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
      $table->decimal('weight', 10, 2)->comment('Product weight')->default(0.3)->nullable();
      $table->decimal('sub_weight', 10, 2)->comment('Total weight each Product')->default(0.3)->nullable();
      $table->integer('stock_in')->default(0)->nullable();
      $table->integer('stock_out')->default(0)->nullable();
      $table->integer('qty_order')->nullable()->nullable();
      $table->integer('qty_indent')->default(0)->nullable();
      $table->enum('product_status', [0, 1, 2, 3, 4])->nullable()
        ->comment('Status product : 0 = Inactive, 1 = Active, 2 = Dis->nullable()abled, 3 = Terminated, 4 = Indent')->default(1);
      $table->enum('stock_type', [1, 2])->comment('Status type : 1 = Stock In, 2 = Stock Out')->default(1);
      $table->integer('daily_stock')->default(0);
      $table->timestamp('daily_stock_date')->nullable();
      $table->integer('weekly_stock')->default(0);
      $table->timestamp('weekly_stock_date')->nullable();
      $table->integer('monthly_stock')->default(0);
      $table->timestamp('monthly_stock_date')->nullable();
      $table->integer('yearly_stock')->default(0);
      $table->timestamp('yearly_stock_date')->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });

    // Table wms_do_headers
    Schema::create('wms_do_headers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->timestamp('do_date');
      $table->uuid('warehouse_uuid')->nullable();
      $table->enum('sent_to', [1, 2, 3, 4, 5])->comment('Sent to : 1 = Warehouse, 2 = Member, 3 = PUC')->default(2)->nullable();
      $table->uuid('to_uuid')->nullable(); // could be member/PUC/warehouse
      $table->string('name')->nullable();
      $table->text('remarks')->nullable();
      $table->text('notes')->nullable();
      $table->text('description')->nullable();
      $table->decimal('total_weight', 10, 2)->comment('Product weight')->default(0.3)->nullable();
      $table->integer('total_stock_in')->default(0)->nullable();
      $table->integer('total_stock_out')->default(0)->nullable();
      $table->integer('total_transaction')->default(0)->nullable(); // total transaction count
      $table->text('transaction_uuids')->nullable();
      $table->integer('total_qty_order')->default(0)->nullable(); // total product fr->nullable()om transaction by do number
      $table->integer('total_qty_sent')->default(0)->nullable();
      $table->integer('total_qty_indent')->default(0)->nullable();
      $table->integer('total_qty_remain')->default(0)->nullable();
      $table->enum('stock_type', [1, 2])->comment('Status type : 1 = Stock In, 2 = Stock Out')->default(1);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });

    // Table wms_do_details
    Schema::create('wms_do_details', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('wms_do_header_uuid')->nullable();
      $table->uuid('product_uuid');
      $table->uuid('product_attribute_uuid')->nullable();
      $table->uuid('product_header_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('attribute_name')->nullable();
      $table->text('description')->nullable();
      $table->enum('is_register', [0, 1]);
      $table->enum('product_status', [0, 1, 2, 3, 4])->nullable()
        ->comment('Status product : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated, 4 = Indent')->default(1);
      $table->decimal('weight', 10, 2)->comment('Product weight')->default(0.3)->nullable();
      $table->enum('stock_type', [1, 2])->comment('Status type : 1 = Stock In, 2 = Stock Out')->default(1);
      $table->integer('qty_order')->default(0)->nullable();
      $table->integer('qty_sent')->default(0)->nullable();
      $table->integer('qty_indent')->default(0)->nullable();
      $table->integer('qty_remain')->default(0)->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
      $table->foreign('wms_do_header_uuid')->references('uuid')->on('wms_do_headers')->onDelete('cascade');
    });

    // Table stock_periods
    Schema::create('stock_periods', function (Blueprint $table) {
      $table->id();
      $table->enum('stock_period', ['daily', 'weekly', 'monthly', 'yearly'])->nullable()
        ->comment('Stock period : daily = every 1 day, weekly = Sunday to Saturday, monthly = date 1 to 31, yearly = 1 jan to 31 dec')->default(1);
      $table->date('start_date');
      $table->string('start_day_name');
      $table->date('end_date');
      $table->string('end_day_name');
      $table->integer('interval_days')->default(0);
      $table->integer('processed_count')->default(0);
      $table->string('name')->nullable();
      $table->timestamps();
      $table->softDeletes();
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
    Schema::dropIfExists('wms_stock_summary_headers');
    Schema::dropIfExists('wms_stock_summary_details');
    Schema::dropIfExists('wms_stock_summary_weekly_headers');
    Schema::dropIfExists('wms_stock_summary_monthly_headers');
    Schema::dropIfExists('wms_stock_summary_yearly_headers');
    Schema::dropIfExists('wms_get_transactions');
    Schema::dropIfExists('wms_do_details');
    Schema::dropIfExists('wms_do_headers');
    Schema::dropIfExists('wms_stock_processes');
    Schema::dropIfExists('stock_periods');
  }
};
