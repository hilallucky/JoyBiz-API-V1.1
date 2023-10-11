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
            $table->uuid('sponsor_uuid')->comment('Sponsor uuid based from table members')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('total_amount_summary', 10, 2)->default(0);
            $table->decimal('ppv', 10, 2)->default(0);
            $table->decimal('pxv', 10, 2)->default(0);
            $table->decimal('pbv', 10, 2)->default(0);
            $table->decimal('prv', 10, 2)->default(0);
            $table->decimal('gpv', 10, 2)->default(0);
            $table->decimal('gxv', 10, 2)->default(0);
            $table->decimal('gbv', 10, 2)->default(0);
            $table->decimal('grv', 10, 2)->default(0);
            $table->decimal('pgpv', 10, 2)->default(0);
            $table->decimal('pgxv', 10, 2)->default(0);
            $table->decimal('pgbv', 10, 2)->default(0);
            $table->decimal('pgrv', 10, 2)->default(0);
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
