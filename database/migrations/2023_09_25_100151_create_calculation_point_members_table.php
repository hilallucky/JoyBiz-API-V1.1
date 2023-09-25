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
        Schema::create('calculation_point_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->uuid('member_uuid')->comment('Member uuid based from table members');
            $table->uuid('rank_uuid')->comment('Rank uuid based from table ranks')->nullable();
            $table->decimal('total_discount_value', 10, 2)->nullable()->default(0);
            $table->decimal('total_discount_value_amount', 10, 2)->default(0);
            $table->decimal('total_price_after_discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('total_shipping_charge', 10, 2)->default(0);
            $table->decimal('total_payment_charge', 10, 2)->default(0);
            $table->decimal('total_amount_summary', 10, 2)->default(0);
            $table->decimal('total_pv', 10, 2)->default(0);
            $table->decimal('total_xv', 10, 2)->default(0);
            $table->decimal('total_bv', 10, 2)->default(0);
            $table->decimal('total_rv', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('member_uuid')->references('uuid')->on('members');
            $table->foreign('rank_uuid')->references('uuid')->on('ranks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calculation_point_members');
    }
};
