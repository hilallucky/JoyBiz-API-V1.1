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
        // Table calculation_point_members
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


        // Table week_periods
        Schema::create('week_periods', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->string('start_day_name');
            $table->date('end_date');
            $table->string('end_day_name');
            $table->integer('interval_days')->default(0);
            $table->string('name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });



        // Table bonus_rank_logs
        Schema::create('bonus_rank_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('process_uuid')->nullable();
            $table->uuid('member_uuid')->comment('Member uuid based from table members');
            $table->integer('rank_id')->comment('Rank id based from table ranks')->nullable();
            $table->uuid('rank_uuid')->comment('Rank uuid based from table ranks')->nullable();
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


        // Table bonus_ranks
        Schema::create('bonus_ranks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('process_uuid')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->uuid('member_uuid')->comment('Member uuid based from table members');
            $table->uuid('rank_uuid')->comment('Rank uuid based from table ranks')->nullable();
            $table->integer('rank_id')->comment('Rank id based from table ranks')->nullable()->default(0);
            $table->uuid('sponsor_uuid')->comment('Sponsor uuid based from table members')->nullable();
            $table->integer('sponsor_id')->default(0);
            $table->decimal('ppv', 12, 2)->nullable();
            $table->decimal('pgv', 12, 2)->nullable();

            $table->decimal('ppv_effective_rank', 12, 2)->default(0);
            $table->decimal('gpv_effective_rank', 12, 2)->default(0);
            $table->integer('mid')->nullable();
            $table->integer('effective_rank_uuid')->nullable();
            $table->integer('effective_rank_id')->nullable();

            $table->decimal('appv', 12, 2)->default(0);
            $table->decimal('apbv', 12, 2)->default(0);
            $table->integer('jbp')->default(0);
            $table->integer('bj')->default(0);
            $table->integer('vj')->default(0);
            $table->integer('current_rank_uuid')->nullable()->comment('Current rank member'); //srank_uuid
            $table->integer('current_rank_id')->default(0); //srank_id
            $table->integer('bj_active')->default(0);
            $table->integer('vj_active')->default(0);

            $table->uuid('bonus_effective_rank_uuid')->comment('Effective rank for bonus calculation, get from table ranks')->nullable();
            $table->integer('bonus_effective_rank_id')->comment('Effective rank for bonus calculation, get from table ranks')->default(0);
            $table->string('created_by')->comment('Created By (User ID from table user')->nullable();
            $table->string('updated_by')->comment('Updated By (User ID from table user')->nullable();
            $table->string('deleted_by')->comment('Deleted By (User ID from table user')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('bonus_weeklies', function (Blueprint $table) {
            $table->id();
            $table->integer('wid');
            $table->integer('year');
            $table->uuid('user_uuid');
            $table->uuid('member_uuid');
            $table->decimal('express', 12, 2)->default(0);
            $table->decimal('productivity', 12, 2)->default(0);
            $table->decimal('team', 12, 2)->default(0);
            $table->decimal('carry_forward', 12, 2)->default(0);
            $table->decimal('mathcing', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('ppn', 12, 2)->default(0);
            $table->decimal('wallet', 12, 2)->default(0);
            $table->decimal('voucher', 12, 2)->default(0);
            $table->decimal('total_transfer', 12, 2)->default(0);
            $table->boolean('confirmed')->default('false');
            $table->boolean('published')->default('false');
            $table->boolean('vouchered')->default('false');
            $table->integer('confirmed_by')->nullable();
            $table->integer('published_by')->nullable();
            $table->integer('vouchered_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


        // Schema::create('eranks', function (Blueprint $table) {
        //     $table->id();
        //     $table->uuid('uuid');
        //     $table->integer('member_uuid'); //jbid
        //     $table->integer('sponsor_uuid')->nullable(); //spid
        //     $table->integer('placement_uuid')->nullable(); //upid
        //     $table->decimal('ppv', 12, 2)->default(0);
        //     $table->decimal('gpv', 12, 2)->default(0);
        //     $table->integer('mid')->nullable();
        //     $table->integer('erank')->default(0);
        //     $table->timestamps();
            // $table->softDeletes();
            // });

        // Schema::create('sranks', function (Blueprint $table) {
        //     $table->id();
        //     $table->uuid('uuid');
        //     $table->integer('member_uuid'); //jbid
        //     $table->integer('sponsor_uuid')->nullable(); //spid
        //     $table->integer('placement_uuid')->nullable(); //upid
        //     $table->decimal('appv', 12, 2)->default(0);
        //     $table->decimal('apbv', 12, 2)->default(0);
        //     $table->integer('jbp')->default(0);
        //     $table->integer('bj')->default(0);
        //     $table->integer('vj')->default(0);
        //     $table->integer('srank')->default(0);
        //     $table->integer('bj_active')->default(0);
        //     $table->integer('vj_active')->default(0);
        //     $table->timestamps();
            // $table->softDeletes();
            // });


        // Schema::create('effective_rank', function (Blueprint $table) {
        //     $table->id();
        //     $table->uuid('uuid');
        //     $table->integer('member_uuid')->nullable();
        //     $table->decimal('ppv', 12, 2)->nullable();
        //     $table->decimal('pgv', 12, 2)->nullable();
        //     $table->integer('month')->nullable();
        //     $table->integer('year')->nullable();
        //     $table->date('date_start')->nullable();
        //     $table->date('date_end')->nullable();
        //     $table->integer('effective_rank')->nullable(); //PERUBAHAN DARI STRING KE INTEGER
        //     $table->timestamps();
            // $table->softDeletes();
            // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calculation_point_members');
        Schema::dropIfExists('week_periods');
        // Schema::dropIfExists('week_periodes');
        Schema::dropIfExists('bonus_rank_logs');
        Schema::dropIfExists('bonus_ranks');
        Schema::dropIfExists('bonus_weeklies');
        // Schema::dropIfExists('eranks');
        // Schema::dropIfExists('sranks');
        // Schema::drop('effective_rank');
    }
};
