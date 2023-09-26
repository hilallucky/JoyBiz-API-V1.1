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
            $table->uuid('process_uuid');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->uuid('member_uuid')->comment('Member uuid based from table members');
            $table->uuid('rank_uuid')->comment('Rank uuid based from table ranks')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('total_amount_summary', 10, 2)->default(0);
            $table->decimal('p_pv', 10, 2)->default(0);
            $table->decimal('p_xv', 10, 2)->default(0);
            $table->decimal('p_bv', 10, 2)->default(0);
            $table->decimal('p_rv', 10, 2)->default(0);
            $table->decimal('g_pv', 10, 2)->default(0);
            $table->decimal('g_xv', 10, 2)->default(0);
            $table->decimal('g_bv', 10, 2)->default(0);
            $table->decimal('g_rv', 10, 2)->default(0);
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
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
