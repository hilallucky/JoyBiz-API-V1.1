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
    // Table members
    Schema::create('members', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('first_name', 100)->comment('First Name');
      $table->string('last_name', 100)->comment('Last Name')->nullable();
      $table->string('id_no', 100)->comment('ID No')->nullable();
      $table->string('phone', 100)->comment('Phone Number')->nullable();
      $table->integer('placement_id')->nullable()->comment('Upline id')->comment('Structurized genealogy');
      $table->string('placement_uuid')->nullable()->comment('Upline uuid')->comment('Structurized genealogy');
      $table->integer('sponsor_id')->nullable()
        ->comment('Sponsor ID - Direct Sponsor uuid, Recruiter genealogy');
      $table->string('sponsor_uuid')->nullable()->comment('Direct Sponsor uuid, Recruiter genealogy');
      $table->integer('user_id')->unique(false)->nullable()->comment('User ID for login in users table');
      $table->string('user_uuid')->comment('User uuid (get from table users)')->nullable();
      $table->uuid('country_uuid')->comment('Country ID (get from table countries')->nullable();
      $table->enum('membership_status', [1, 2, 3])->nullable()
        ->comment('Membership Status : 1 = Member, 2 = Special Customer')->default(1);
      $table->enum('status', [0, 1, 2, 3])->nullable()
        ->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Dormant, 4 = Terminated, 5 = Pending')
        ->default(1);
      $table->date('will_dormant_at')->comment('Date Member Will dormant, statu = 3')->nullable();
      $table->integer('min_bv')->default(0)->comment('Minimum required BV');
      $table->text('remarks')->comment('Notes of members')->nullable();
      $table->string('created_by')->comment('Created By in members (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By in members (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By in members (User ID from table user')->nullable();
      $table->date('activated_at')->comment('Membership activate date')->nullable();
      $table->string('activated_by')->comment('Activated By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    // Table member_addresses
    Schema::create('member_addresses', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('member_uuid')->comment('Get from table members');
      $table->uuid('city_uuid')->comment('Get from table cities')->nullable();
      $table->string('zip_code', 10);
      $table->string('province', 100);
      $table->string('city', 100);
      $table->string('district', 100);
      $table->string('village', 30);
      $table->string('details')->nullable();
      $table->string('notes')->nullable();
      $table->string('remarks')->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

      // $table->foreign('created_by')->references('uuid')->on('users');
      // $table->foreign('updated_by')->references('uuid')->on('users');
      // $table->foreign('deleted_by')->references('uuid')->on('users');
      $table->foreign('member_uuid')->references('uuid')->on('members')->onDelete('cascade');

      $table->timestamps();
      $table->softDeletes();
    });

    // Table members
    Schema::create('member_shipping_addresses', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('member_uuid')->comment('Get from table members');
      $table->uuid('receiver_name')->comment('Receiver Name')->nullable();
      $table->uuid('receiver_phone')->comment('Receiver Phone')->nullable();
      $table->uuid('city_uuid')->comment('Get from table cities')->nullable();
      $table->string('zip_code', 10);
      $table->string('province', 100);
      $table->string('city', 100);
      $table->string('district', 100);
      $table->string('village', 30);
      $table->string('details')->nullable();
      $table->string('notes')->nullable();
      $table->string('remarks')->nullable();
      $table->enum('status', [0, 1])->nullable()->comment('Status : 0 = Inactive, 1 = Active')->default(1);
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();

      // $table->foreign('created_by')->references('uuid')->on('users');
      // $table->foreign('updated_by')->references('uuid')->on('users');
      // $table->foreign('deleted_by')->references('uuid')->on('users');
      $table->foreign('member_uuid')->references('uuid')->on('members')->onDelete('cascade');

      $table->timestamps();
      $table->softDeletes();
    });


    // Table users
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('first_name', 100)->description('Member first name');
      $table->string('last_name', 100)->description('Member last name')->nullable();
      $table->string('email')->unique()->description('Member email')->nullable();
      $table->string('validation_code')->comment('Validation code with link')->after('email_verified_at');
      $table->dateTime('email_verified_at')->nullable()->description('Member email verification date');
      $table->string('password')->description('Member password');
      $table->timestamp('last_logged_in')->nullable()->description('Member latest login');
      $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')->default(1);
      $table->text('remarks')->comment('Notes of users')->nullable();
      $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
      $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
      $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    // Table carts
    Schema::create('carts', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('member_uuid');
      $table->uuid('product_uuid');
      $table->uuid('product_price_uuid')->comment('Get from table product_prices');
      $table->integer('qty')->default(1);
      $table->timestamps();
      $table->softDeletes();
    });


    // Table whislists
    Schema::create('whislists', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->uuid('member_uuid');
      $table->uuid('product_uuid');
      $table->uuid('product_price_uuid')->comment('Get from table product_prices');
      $table->integer('qty')->default(1);
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
    Schema::dropIfExists('members');
    Schema::dropIfExists('member_addresses');
    Schema::dropIfExists('member_shipping_addresses');
    Schema::dropIfExists('users');
    Schema::dropIfExists('carts');
    Schema::dropIfExists('whislists');
  }
};
