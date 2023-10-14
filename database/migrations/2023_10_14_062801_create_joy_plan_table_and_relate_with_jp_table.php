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
        Schema::create('prepared_data_joys', function (Blueprint $table) {
            $table->id();
            $table->integer('wid');
            $table->integer('member_uuid');
            $table->integer('sponsor_uuid')->nullable();
            $table->integer('placement_uuid')->nullable();
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
            $table->integer('current_rank')->default(0);
            $table->integer('effective_rank')->default(0);
            $table->timestamps();
        });


        Schema::create('joy_bonus_summaries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->date('date');
            $table->integer('wid');
            $table->uuid('member_uuid');
            $table->uuid('owner');
            $table->decimal('xpress', 10, 2)->default(0);
            $table->decimal('bgroup', 10, 2)->default(0);
            $table->decimal('leadership', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('tax', 8, 2)->default(0);
            $table->decimal('voucher', 10, 2)->default(0);
            $table->decimal('transfer', 10, 2)->default(0);
            $table->boolean('confirmed')->default(false);
            $table->boolean('published')->default(false);
            $table->string('vouchered')->nullable();
            $table->date('transfered')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('joy_point_rewards', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->date('date');
            $table->uuid('member_uuid');
            $table->integer('joy')->default(0);
            $table->integer('biz')->default(0);
            $table->integer('joy_rv')->default(0);
            $table->integer('biz_rv')->default(0);
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
        Schema::dropIfExists('prepared_data_joys');
        Schema::dropIfExists('joy_bonus_summaries');
        Schema::dropIfExists('joy_point_rewards');
    }
};
