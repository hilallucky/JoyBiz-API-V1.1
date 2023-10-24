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
    // Table order_group_headers
    Schema::create('order_group_headers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('order_group_header_temp_uuid')->nullable();
      $table->uuid('member_uuid')->comment('Get from table members');
      $table->text('remarks')->comment('Notes of product prices')->nullable();
      $table->decimal('total_discount_value', 10, 2)->default(0);
      $table->decimal('total_discount_value_amount', 10, 2)->default(0);
      $table->decimal('total_voucher_amount', 10, 2)->default(0);
      // total_amount = total price product original 
      $table->decimal('total_amount', 10, 2)->default(0);
      // total_amount_after_discount = total price product original - total_discount_value_amount
      $table->decimal('total_amount_after_discount', 10, 2)->default(0);
      $table->decimal('total_cashback', 10, 2)->default(0);
      $table->decimal('total_cashback_reseller', 10, 2)->default(0);
      $table->decimal('total_shipping_charge', 10, 2)->default(0);
      $table->decimal('total_shipping_discount', 10, 2)->default(0);
      $table->decimal('total_shipping_nett', 10, 2)->default(0);
      $table->decimal('total_payment_charge', 10, 2)->default(0);
      $table->decimal('tax_amount', 10, 2)->default(0);
      // total_charge = total_shipping_nett + total_payment_charge
      $table->decimal('total_charge', 10, 2)->default(0);
      // total_amount_summary = (total_amount_after_discount + tax_amount)  - (total_voucher_amount + total_payment_charge)
      $table->decimal('total_amount_summary', 10, 2)->default(0);
      $table->decimal('total_pv', 10, 2)->default(0);
      $table->decimal('total_xv', 10, 2)->default(0);
      $table->decimal('total_bv', 10, 2)->default(0);
      $table->decimal('total_rv', 10, 2)->default(0);
      $table->decimal('total_order_to_shipped', 10, 2)->default(0);
      $table->decimal('total_order_to_picked_up', 10, 2)->default(0);
      $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Pending, 1 = Paid, 2 = Posted, 3 = Rejected')->default(0);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->date('transaction_date')->comment('Transaction date');
      $table->softDeletes();
      $table->timestamps();
    });

    // Table order_group_payments
    Schema::create('order_group_payments', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('order_group_header_uuid')->nullable();
      $table->uuid('order_group_header_temp_uuid')->nullable();
      $table->uuid('payment_type_uuid')->comment('Get from table payment_types');
      $table->decimal('amount', 10, 2)->default(0);
      $table->text('remarks')->comment('Notes of payment type')->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

      $table->foreign('order_group_header_uuid')->references('uuid')->on('order_group_headers')->onDelete('cascade');
      $table->foreign('payment_type_uuid')->references('uuid')->on('payment_types')->onDelete('cascade');

      $table->softDeletes();
      $table->timestamps();
    });

    // Table order_headers
    Schema::create('order_headers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('order_header_temp_uuid')->unique();
      $table->uuid('order_group_header_uuid')->nullable();
      $table->uuid('order_group_header_temp_uuid')->nullable();
      $table->uuid('member_uuid')->comment('Get from table members');
      $table->uuid('price_code_uuid')->comment('Get from table price_codes');
      $table->text('remarks')->comment('Notes of product prices')->nullable();
      $table->decimal('total_discount_value', 10, 2)->default(0);
      $table->decimal('total_discount_value_amount', 10, 2)->default(0);
      $table->decimal('total_voucher_amount', 10, 2)->default(0);
      // total_amount = total price product original 
      $table->decimal('total_amount', 10, 2)->default(0);
      // total_amount_after_discount = total price product original - total_discount_value_amount
      $table->decimal('total_amount_after_discount', 10, 2)->default(0);
      $table->decimal('total_cashback', 10, 2)->default(0);
      $table->decimal('total_cashback_reseller', 10, 2)->default(0);
      $table->decimal('total_shipping_charge', 10, 2)->default(0);
      $table->decimal('total_shipping_discount', 10, 2)->default(0);
      $table->decimal('total_shipping_nett', 10, 2)->default(0);
      $table->decimal('total_payment_charge', 10, 2)->default(0);
      $table->decimal('tax_amount', 10, 2)->default(0);
      // total_charge = total_shipping_nett + total_payment_charge
      $table->decimal('total_charge', 10, 2)->default(0);
      // total_amount_summary = (total_amount_after_discount + tax_amount) - (total_voucher_amount + total_payment_charge)
      $table->decimal('total_amount_summary', 10, 2)->default(0);
      $table->decimal('total_pv', 10, 2)->default(0);
      $table->decimal('total_xv', 10, 2)->default(0);
      $table->decimal('total_bv', 10, 2)->default(0);
      $table->decimal('total_rv', 10, 2)->default(0);
      $table->decimal('total_pv_plan_joy', 10, 2)->default(0);
      $table->decimal('total_bv_plan_joy', 10, 2)->default(0);
      $table->decimal('total_rv_plan_joy', 10, 2)->default(0);
      $table->decimal('total_pv_plan_biz', 10, 2)->default(0);
      $table->decimal('total_bv_plan_biz', 10, 2)->default(0);
      $table->decimal('total_rv_plan_biz', 10, 2)->default(0);
      $table->decimal('total_price_joy', 10, 2)->default(0);
      $table->decimal('total_price_biz', 10, 2)->default(0);
      $table->decimal('total_price_with_bv', 10, 2)->default(0);
      $table->enum('ship_type', [0, 1, 2])->nullable()
        ->comment('Shipping Type : 0 = Hold, 1 = Pickup, 2 = Ship To Address')
        ->default(1);
      $table->enum('status', [0, 1, 2, 3, 4, 5, 6, 7])->nullable()
        ->comment('Status : 0 = Pending, 1 = Paid, 2 = Posted, 3 = Rejected, 4 = Waiting For Prepared, 5 = Prepared From Warehouse, 6 = Picked Up By Courier, 7 = Delivered')
        ->default(0);
      $table->string('airway_bill_no')->comment('AWB/Resi No, only if ship_type = 2')->nullable();
      $table->uuid('calculation_point_process_uuid')->nullable()
        ->comment('Fill this field, if transaction already process to table calculation_point_members');
      $table->date('calculation_date')->nullable()->comment('Calculation date');
      $table->string('created_by')->comment('Created By in order_headers (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By in order_headers (User ID from table user')->nullable();
      $table->uuid('deleted_by')->comment('Deleted By in order_headers (User ID from table user')->nullable();
      $table->date('transaction_date')->comment('Transaction date');
      $table->date('approved_date')->comment('Approved date')->nullable();
      $table->string('approved_by')->comment('Approved By (User ID from table user')->nullable();

      // $table->foreign('created_by')->references('uuid')->on('users');
      // $table->foreign('updated_by')->references('uuid')->on('users');
      // $table->foreign('deleted_by')->references('uuid')->on('users');

      $table->foreign('order_header_temp_uuid')->references('uuid')->on('order_headers_temp')->onDelete('cascade');
      // // $table->foreign('user_uuid')->references('uuid')->on('users')->onDelete('cascade');
      $table->foreign('member_uuid')->references('uuid')->on('members')->onDelete('cascade');
      $table->foreign('price_code_uuid')->references('uuid')->on('price_codes')->onDelete('cascade');
      // $table->foreign('shipping_uuid')->references('uuid')->on('couriers');

      $table->softDeletes();
      $table->timestamps();
    });


    // Table order_details
    Schema::create('order_details', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('order_detail_temp_uuid');
      $table->uuid('order_group_header_uuid')->nullable();
      $table->uuid('order_group_header_temp_uuid')->nullable();
      $table->uuid('order_header_uuid');
      $table->uuid('product_uuid')->comment('Get from table products');
      $table->uuid('product_price_uuid')->comment('Get from table product_prices');
      $table->integer('qty')->default(1);
      $table->decimal('price', 10, 2)->default(0);
      $table->enum('discount_type', ['percentage', 'amount'])->nullable();
      $table->decimal('discount_value', 10, 2)->nullable()->default(0);
      $table->decimal('discount_value_amount', 10, 2)->default(0);
      $table->decimal('price_after_discount', 10, 2)->default(0);
      $table->decimal('cashback', 10, 2)->default(0);
      $table->decimal('cashback_reseller', 10, 2)->default(0);
      $table->decimal('pv', 10, 2)->default(0);
      $table->decimal('xv', 10, 2)->default(0);
      $table->decimal('bv', 10, 2)->default(0);
      $table->decimal('rv', 10, 2)->default(0);
      $table->enum('status', [0, 1, 2, 3, 4])->nullable()
        ->comment('Status product : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated, 4 = Indent')->default(1);
      $table->enum('do_status', [0, 1, 2, 3, 4])->nullable()
        ->comment('Status DO Product : 0 = Pending, 1 = Processed DO, 2 = Hold, 3 = Canceled')->default(0);
      $table->string('do_n;umber')->nullable()->comment('Only if warehouse process th DO (do_status = 1)')->nullable();
      $table->string('created_by')->comment('Created By in order_details (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By in order_details (User ID from table user')->nullable();
      $table->uuid('deleted_by')->comment('Deleted By in order_details (User ID from table user')->nullable();

      $table->foreign('order_detail_temp_uuid')->references('uuid')->on('order_details_temp'); //->onDelete('cascade');
      $table->foreign('order_header_uuid')->references('uuid')->on('order_headers'); //->onDelete('cascade');
      $table->foreign('product_uuid')->references('uuid')->on('products')->onDelete('cascade');
      $table->foreign('product_price_uuid')->references('uuid')->on('product_prices')->onDelete('cascade');

      // $table->foreign('created_by')->references('uuid')->on('users');
      // $table->foreign('updated_by')->references('uuid')->on('users');
      // $table->foreign('deleted_by')->references('uuid')->on('users');

      $table->softDeletes();
      $table->timestamps();
    });


    // Table order_payments
    Schema::create('order_payments', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('order_payment_temp_uuid');
      $table->uuid('order_group_header_uuid')->nullable();
      $table->uuid('order_group_header_temp_uuid')->nullable();
      $table->uuid('order_header_uuid');
      $table->uuid('payment_type_uuid')->comment('Get from table payment_types');
      $table->uuid('voucher_uuid')->nullable()->comment('Voucher reference if payment using voucher.');
      $table->uuid('voucher_code')->nullable()->comment('Voucher code reference if payment using voucher.');
      $table->decimal('amount', 10, 2)->default(0);
      $table->decimal('discount', 10, 2)->default(0);
      $table->decimal('amount_after_discount', 10, 2)->default(0);
      $table->text('remarks')->comment('Notes of payment type')->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

      $table->foreign('order_payment_temp_uuid')->references('uuid')->on('order_payments_temp')->onDelete('cascade');
      $table->foreign('order_header_uuid')->references('uuid')->on('order_headers')->onDelete('cascade');
      $table->foreign('payment_type_uuid')->references('uuid')->on('payment_types')->onDelete('cascade');

      // $table->foreign('created_by')->references('uuid')->on('users');
      // $table->foreign('updated_by')->references('uuid')->on('users');
      // $table->foreign('deleted_by')->references('uuid')->on('users');

      $table->softDeletes();
      $table->timestamps();
    });


    // Table order_shipping
    Schema::create('order_shipping', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('order_shipping_temp_uuid');
      $table->uuid('order_group_header_uuid')->nullable();
      $table->uuid('order_group_header_temp_uuid')->nullable();
      $table->uuid('order_header_uuid');
      $table->uuid('courier_uuid')->comment('Get from table couriers');
      $table->decimal('shipping_charge', 10, 2)->default(0);
      $table->decimal('discount_shipping_charge', 10, 2)->default(0);
      $table->string('receiver_name')->comment('Receiver Name')->nullable();
      $table->string('receiver_phone_number')->comment('Receiver Phone Number')->nullable();
      $table->uuid('member_shipping_address_uuid')->comment('Get from table member_shipping_addresses')->nullable();
      $table->string('province')->comment('Province Name')->nullable();
      $table->string('city')->comment('City Name')->nullable();
      $table->string('district')->comment('District Name')->nullable();
      $table->string('village')->comment('Village Name')->nullable();
      $table->string('details')->comment('Address Detail')->nullable();
      $table->string('notes')->comment('Address Notes')->nullable();
      $table->string('remarks')->comment('Address remarks')->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

      $table->foreign('order_shipping_temp_uuid')->references('uuid')->on('order_shipping_temp')->onDelete('cascade');
      $table->foreign('order_header_uuid')->references('uuid')->on('order_headers')->onDelete('cascade');
      $table->foreign('courier_uuid')->references('uuid')->on('couriers')->onDelete('cascade');
      $table->foreign('member_shipping_address_uuid')->references('uuid')->on('member_shipping_addresses')->onDelete('cascade');

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
    Schema::dropIfExists('order_group_headers');
    Schema::dropIfExists('order_group_payments');
    Schema::dropIfExists('order_headers');
    Schema::dropIfExists('order_details');
    Schema::dropIfExists('order_payments');
    Schema::dropIfExists('order_shipping');
  }
};
