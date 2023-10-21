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
    // Table countries
    Schema::create('countries', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('name', 100)->nullable()->comment('Country Name');
      $table->string('name_iso', 100)->nullable()->comment('Country Name - ISO');
      $table->string('region_name', 100)->nullable()->comment('Region Name');
      $table->string('sub_region_name', 100)->nullable()->comment('Sub Region Name');
      $table->string('intermediate_region_name', 100)->nullable()->comment('Intermediate Region Name');
      $table->string('capital_city', 100)->nullable()->comment('Capital City');
      $table->string('tld', 100)->nullable()->comment('TLD');
      $table->string('languages', 100)->nullable()->comment('Languages');
      $table->integer('geoname_id')->nullable()->comment('Geoname ID');
      $table->string('dial_prefix', 100)->nullable()->comment('Dial Prefix');
      $table->string('alpha_2_iso', 100)->nullable()->comment('ISO 2 Char');
      $table->string('alpha_3_iso', 100)->nullable()->comment('ISO 3 Char');
      $table->string('corrency_code_iso', 100)->nullable()->comment('corrency ode ISO');
      $table->string('currency_minor_unit_iso', 100)->nullable()->comment('Geoname ID');
      $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active')->default(1);
      $table->text('remarks')->comment('Notes of countries')->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    // Table cities
    Schema::create('cities', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('country_uuid')->nullable();
      $table->string('price_code')->nullable();
      $table->uuid('price_code_uuid')->nullable();
      $table->string('area_code', 30)->nullable();
      $table->string('zip_code', 10)->nullable();
      $table->string('province', 100);
      $table->string('city', 100);
      $table->string('district', 100)->nullable();
      $table->string('village', 100)->nullable();
      $table->string('latitude', 100)->nullable();
      $table->string('longitude', 100)->nullable();
      $table->string('elevation', 100)->nullable();
      $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active')->default(1);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    // Table payment_types
    Schema::create('payment_types', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      // $table->uuid('ref_id')->nullable()->comment('Refference id from up Payment Type');
      $table->uuid('ref_uuid')->nullable()->comment('Refference uuid from up Payment Type');
      $table->string('code')->unique()->comment('Short code of payment type');
      $table->string('name')->unique()->comment('Name of payment type');
      $table->string('description')->comment('Description of payment type')->nullable();
      $table->decimal('charge_percent')->comment('Payment charge')->default('0.00');
      $table->decimal('charge_amount')->comment('Payment charge')->default('0.00');
      $table->string('effect', 2)->comment('Effect : -,+,/,*')->default('+');
      $table->enum('status_web', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active')->default(1);
      $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active')->default(1);
      $table->text('remarks')->comment('Notes of payment type')->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->uuid('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    // Table couriers
    Schema::create('couriers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('gallery_uuid')->nullable();
      $table->string('code');
      $table->string('name');
      $table->string('short_name');
      $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active')->default(1);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    // Table ranks
    Schema::create('ranks', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->tinyInteger('rank_id');
      $table->string('gallery_uuid')->nullable();
      $table->string('name')->nullable();
      $table->string('short_name');
      $table->text('description');
      $table->integer('acc_pbv')->default(0);
      $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active')->default(1);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    // Table clients
    Schema::create('clients', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('ip', 100)->comment('Client IP')->nullable();
      $table->string('domain_name', 100)->comment('Client Domaian Name')->nullable();
      $table->string('client_key')->comment('Client Key');
      $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active')->default(1);
      $table->string('description')->comment('Description')->nullable();
      $table->string('phone', 100)->comment('Phone Number')->nullable();
      $table->uuid('country_id')->comment('Country ID (get from table countries')->nullable();
      $table->text('remarks')->comment('Notes of clients')->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    // Table clients
    Schema::create('warehouses', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('code', 25)->unique();
      $table->string('name');
      $table->string('phone');
      $table->string('mobile_phone', 25);
      $table->string('email', 150);
      $table->string('province')->comment('Province Name')->nullable();
      $table->string('city')->comment('City Name')->nullable();
      $table->string('district')->comment('District Name')->nullable();
      $table->string('village')->comment('Village Name')->nullable();
      $table->string('zip_code')->comment('Zip Code')->nullable();
      $table->string('details')->comment('Address Detail')->nullable();
      $table->text('description')->nullable();
      $table->string('notes')->nullable();
      $table->string('remarks')->nullable();
      $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')->default(1);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
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
    Schema::dropIfExists('countries');
    Schema::dropIfExists('cities');
    Schema::dropIfExists('payment_types');
    Schema::dropIfExists('couriers');
    Schema::dropIfExists('ranks');
    Schema::dropIfExists('clients');
    Schema::dropIfExists('warehouses');
  }
};
