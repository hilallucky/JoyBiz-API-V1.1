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
    // Table Suppliers
    Schema::create('suppliers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('first_name', 100)->comment('First Name');
      $table->string('last_name', 100)->comment('Last Name')->nullable();
      $table->string('mobile_phone', 100)->comment('Mobile Phone Number')->nullable();
      $table->string('phone', 100)->comment('Phone Number')->nullable();
      $table->uuid('country_uuid')->comment('Country ID (get from table countries')->nullable();
      $table->uuid('city_uuid')->comment('Get from table cities')->nullable();
      $table->string('zip_code', 10);
      $table->string('province', 100);
      $table->string('city', 100);
      $table->string('district', 100);
      $table->string('village', 30);
      $table->string('details')->nullable();
      $table->string('notes')->nullable();
      $table->enum('status', [0, 1, 2, 3])->nullable()
        ->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Dormant, 4 = Terminated, 5 = Pending')
        ->default(1);
      $table->text('remarks')->comment('Notes of members')->nullable();
      $table->string('created_by')->comment('Created By in members (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By in members (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By in members (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });

    // Table purchase_headers
    Schema::create('purchase_headers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->date('purchased_date')->comment('Purchased Date');
      $table->string('purchased_by')->comment('Purchased By');
      $table->uuid('supplier_uuid')->comment('Get from table suppliers');
      $table->text('remarks')->comment('Notes of product prices')->nullable();
      $table->decimal('total_qty', 10, 2)->default(0);
      $table->decimal('total_amount', 10, 2)->default(0);
      $table->decimal('total_discount_value', 10, 2)->default(0);
      $table->decimal('total_amount_after_discount', 10, 2)->default(0);
      $table->decimal('total_shipping_charge', 10, 2)->default(0);
      $table->decimal('total_shipping_discount', 10, 2)->default(0);
      $table->decimal('total_shipping_nett', 10, 2)->default(0);
      $table->decimal('total_payment_charge', 10, 2)->default(0);
      $table->decimal('tax_amount', 10, 2)->default(0);
      $table->decimal('total_charge', 10, 2)->default(0);
      $table->decimal('total_amount_summary', 10, 2)->default(0);
      $table->date('approved_date')->comment('Approved date');
      $table->string('approved_by')->comment('Approved By');
      $table->date('paid_date')->comment('Paid date');
      $table->string('paid_by')->comment('Paid By');
      $table->date('shipped_date')->comment('Shipped date');
      $table->date('received_date')->comment('Received Date');
      $table->string('received_by')->comment('Received By');
      $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Pending, 1 = Approved, 2 = Paid, 3 = On The Way, 4 = Received By Warehouse, 10 =Rejected')->default(0);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

      $table->foreign('supplier_uuid')->references('uuid')->on('supplier_uuid')->onDelete('cascade');

      $table->softDeletes();
      $table->timestamps();
    });


    // Table purchase_details
    Schema::create('purchase_details', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('purchase_header_uuid');
      $table->uuid('product_uuid')->comment('Get from table products');
      $table->uuid('product_attribute_uuid')->comment('Get from table product attributes');
      $table->integer('qty')->default(1);
      $table->decimal('price', 10, 2)->default(0);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

      $table->foreign('purchase_header_uuid')->references('uuid')->on('purchase_headers')->onDelete('cascade');
      $table->foreign('product_uuid')->references('uuid')->on('products')->onDelete('cascade');
      $table->foreign('product_attribute_uuid')->references('uuid')->on('product_attributes')->onDelete('cascade');

      $table->softDeletes();
      $table->timestamps();
    });


    // Table purchase_payments
    Schema::create('purchase_payments', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('purchase_header_uuid');
      $table->uuid('payment_type_uuid')->comment('Get from table payment_types');
      $table->decimal('amount', 10, 2)->default(0);
      $table->decimal('discount', 10, 2)->default(0);
      $table->decimal('amount_after_discount', 10, 2)->default(0);
      $table->text('remarks')->comment('Notes of payment type')->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

      $table->foreign('purchase_header_uuid')->references('uuid')->on('purchase_headers')->onDelete('cascade');
      $table->foreign('payment_type_uuid')->references('uuid')->on('payment_types')->onDelete('cascade');

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
    Schema::dropIfExists('suppliers');
    Schema::dropIfExists('purchase_headers');
    Schema::dropIfExists('purchase_details');
    Schema::dropIfExists('purchase_payments');
  }
};
