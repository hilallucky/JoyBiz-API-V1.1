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
        Schema::create('bonus_ranks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('process_uuid');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->uuid('member_uuid')->comment('Member uuid based from table members');
            $table->uuid('rank_uuid')->comment('Rank uuid based from table ranks')->nullable();
            $table->uuid('sponsor_uuid')->comment('Sponsor uuid based from table members')->nullable();
            $table->integer('sponsor_id')->default(0);
            $table->decimal('ppv', 12, 2)->nullable();
            $table->decimal('pgv', 12, 2)->nullable();

            $table->decimal('ppv_erank', 12, 2)->default(0);
            $table->decimal('gpv_erank', 12, 2)->default(0);
            $table->integer('mid')->nullable();
            $table->integer('erank_uuid')->nullable();

            $table->decimal('appv', 12, 2)->default(0);
            $table->decimal('apbv', 12, 2)->default(0);
            $table->integer('jbp')->default(0);
            $table->integer('bj')->default(0);
            $table->integer('vj')->default(0);
            $table->integer('srank_uuid')->nullable();
            $table->integer('srank_id')->default(0);
            $table->integer('bj_active')->default(0);
            $table->integer('vj_active')->default(0);

            $table->uuid('effective_rank_uuid')->comment('Effective rank, get from table ranks')->nullable();
            $table->integer('effective_rank_id')->comment('Effective rank, get from table ranks')->default(0);
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
        Schema::dropIfExists('bonus_ranks');
    }
};
