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

    Schema::create('vouchers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->enum('type', [1, 2, 3])->comment('Type : 1 = V-Cash, 2 = V-Product, 3 = V-Promo')->default(1);
      $table->uuid('member_uuid'); //owner
      $table->string('saldo');
      $table->date('expired_date')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('voucher_details', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->uuid('member_uuid'); //owner
      $table->string('code');
      $table->string('debit');
      $table->string('credit');
      $table->uuid('transaction_uuid')->nullable();
      $table->text('note');
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
    Schema::dropIfExists('vouchers');
    Schema::dropIfExists('voucher_details');
  }
};
