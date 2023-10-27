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


    Schema::create('eranks', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->uuid('member_uuid'); //jbid
      $table->uuid('sponsor_uuid')->nullable(); //spid
      $table->uuid('placement_uuid')->nullable(); //upid
      $table->decimal('ppv', 12, 2)->default(0);
      $table->decimal('gpv', 12, 2)->default(0);
      $table->integer('mid')->nullable();
      $table->uuid('mid_uuid')->nullable();
      $table->integer('erank')->default(0);
      $table->uuid('erank_uuid')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });

    Schema::create('sranks', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->uuid('member_uuid'); //jbid
      $table->uuid('sponsor_uuid')->nullable(); //spid
      $table->uuid('placement_uuid')->nullable(); //upid
      $table->decimal('appv', 12, 2)->default(0);
      $table->decimal('apbv', 12, 2)->default(0);
      $table->integer('jbp')->default(0);
      $table->integer('bj')->default(0);
      $table->integer('vj')->default(0);
      $table->integer('srank')->default(0);
      $table->string('srank_uuid')->nullable();
      $table->integer('bj_active')->default(0);
      $table->integer('vj_active')->default(0);
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('effective_rank', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->uuid('member_uuid')->nullable();
      $table->decimal('ppv', 12, 2)->nullable();
      $table->decimal('pgv', 12, 2)->nullable();
      $table->integer('month')->nullable();
      $table->integer('year')->nullable();
      $table->date('date_start')->nullable();
      $table->date('date_end')->nullable();
      $table->integer('effective_rank')->nullable(); //PERUBAHAN DARI STRING KE INTEGER
      $table->uuid('effective_rank_uuid')->nullable(); //PERUBAHAN DARI STRING KE INTEGER
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('carry_forward_details', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->integer('wid');
      $table->uuid('wid_uuid')->nullable();
      $table->uuid('member_uuid');
      $table->decimal('gpvj');
      $table->decimal('gbvj');
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('prepared_data_joys', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->integer('wid');
      $table->uuid('wid_uuid')->nullable();
      $table->uuid('member_uuid'); //jbid
      $table->uuid('sponsor_uuid')->nullable(); //spid
      $table->uuid('placement_uuid')->nullable(); //upid
      $table->decimal('ppv', 12, 2)->default(0);
      $table->decimal('pbv', 12, 2)->default(0);
      $table->decimal('prv', 12, 2)->default(0);
      $table->decimal('ppvj', 12, 2)->default(0);
      $table->decimal('pbvj', 12, 2)->default(0);
      $table->decimal('prvj', 12, 2)->default(0);
      $table->decimal('gpvj', 12, 2)->default(0);
      $table->decimal('gbvj', 12, 2)->default(0);
      $table->decimal('grvj', 12, 2)->default(0);
      $table->decimal('omzet', 12, 2)->default(0);
      $table->decimal('ozj', 12, 2)->default(0);
      $table->decimal('opc', 12, 2)->default(0);
      $table->integer('srank')->default(0);
      $table->uuid('srank_uuid')->nullable();
      $table->integer('erank')->default(0);
      $table->uuid('erank_uuid')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('joy_carry_forwards', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->date('date');
      $table->uuid('member_uuid'); //owner
      $table->integer('big_leg')->nullable();
      $table->integer('big_bv')->default(0);
      $table->integer('small_leg')->nullable();
      $table->integer('small_bv')->default(0);
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('joy_rv_forwards', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->date('date');
      $table->uuid('member_uuid'); //owner
      $table->integer('big_leg')->nullable();
      $table->integer('big_rv')->default(0);
      $table->integer('small_leg')->nullable();
      $table->integer('small_rv')->default(0);
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('joy_point_rewards', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->date('date');
      $table->uuid('member_uuid');
      $table->integer('joy')->default(0);
      $table->integer('biz')->default(0);
      $table->integer('joy_rv')->default(0);
      $table->integer('biz_rv')->default(0);
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('vital_signs', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->integer('year');
      $table->integer('month');
      $table->uuid('member_uuid'); //owner
      $table->integer('g1')->default(0);
      $table->integer('abg')->default(0);
      $table->integer('depth1')->default(0);
      $table->integer('depth2')->default(0);
      $table->timestamps();
      $table->softDeletes();
    });


    // ================== Start Plan Biz ==================

    Schema::create('prepared_data_bizs', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->integer('mid');
      $table->uuid('member_uuid');
      $table->uuid('sponsor_uuid')->nullable();
      $table->uuid('placement_uuid')->nullable();
      $table->decimal('ppv', 12, 2)->default(0);
      $table->decimal('pbv', 12, 2)->default(0);
      $table->decimal('gpv', 12, 2)->default(0);
      $table->decimal('gbv', 12, 2)->default(0);
      $table->decimal('ppvb', 12, 2)->default(0);
      $table->decimal('pbvb', 12, 2)->default(0);
      $table->decimal('gpvb', 12, 2)->default(0);
      $table->decimal('gbvb', 12, 2)->default(0);
      $table->decimal('omzet', 12, 2)->default(0);
      $table->decimal('ozb', 12, 2)->default(0);
      $table->decimal('gpvb_under_100', 12, 2)->default(0);
      $table->decimal('gbvb_under_100', 12, 2)->default(0);
      $table->integer('srank')->default(0);
      $table->uuid('srank_uuid')->nullable();
      $table->integer('erank')->default(0);
      $table->uuid('erank_uuid')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });
    // ================== End Plan Biz ==================


    //Coupons

    Schema::create('monthly_reward_coupons', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->uuid('member_uuid'); //owner
      // $table->uuid('owner');
      $table->string('voucher');
      $table->string('mid');
      $table->boolean('active')->default(true);
      $table->timestamps();
      $table->softDeletes();
    });


    // Vouchers

    Schema::create('vouchers', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->uuid('member_uuid'); //owner
      // $table->uuid('owner');
      $table->string('saldo');
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('voucher_details', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->uuid('member_uuid'); //owner
      // $table->uuid('owner');
      $table->string('code');
      $table->string('debit');
      $table->string('credit');
      $table->text('note');
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('voucher_cashbacks', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->uuid('member_uuid'); //owner
      // $table->uuid('owner_id');
      $table->decimal('amount', 12, 2)->default(0);
      $table->string('encrypted_amount')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('voucher_cashback_details', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->uuid('member_uuid'); //owner
      // $table->uuid('owner_id');
      $table->string('code');
      $table->boolean('credit')->default('true');
      $table->string('amount')->nullable();
      $table->string('encrypted_amount')->nullable();
      $table->string('description')->nullable();
      $table->string('transaction_code')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });


    // Wallets

    Schema::create('wallets', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->uuid('member_uuid'); //owner
      // $table->uuid('owner');
      $table->string('saldo');
      $table->timestamps();
      $table->softDeletes();
    });


    Schema::create('wallet_details', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->nullable();
      $table->uuid('member_uuid'); //owner
      // $table->uuid('owner');
      $table->string('code');
      $table->string('debit');
      $table->string('credit');
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
    Schema::dropIfExists('eranks');
    Schema::dropIfExists('sranks');
    Schema::dropIfExists('effective_rank');
    Schema::dropIfExists('vital_signs');
    Schema::dropIfExists('carry_forward_details');
    Schema::dropIfExists('prepared_data_joys');
    Schema::dropIfExists('joy_carry_forwards');
    Schema::dropIfExists('joy_rv_forwards');
    Schema::dropIfExists('joy_point_rewards');
    Schema::dropIfExists('prepared_data_bizs');
    Schema::dropIfExists('monthly_reward_coupons');
    Schema::dropIfExists('vouchers');
    Schema::dropIfExists('voucher_details');
    Schema::dropIfExists('voucher_cashbacks');
    Schema::dropIfExists('voucher_cashback_details');
    Schema::dropIfExists('wallets');
    Schema::dropIfExists('wallet_details');
  }
};
