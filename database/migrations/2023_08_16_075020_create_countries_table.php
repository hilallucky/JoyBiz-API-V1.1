<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
            $table->enum('status', [0, 1, 2, 3])->nullable()->comment('Status : 0 = Inactive, 1 = Active, 2 = Disabled, 3 = Terminated')->default(1);
            $table->text('remarks')->comment('Notes of countries')->nullable();
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
    }
};
