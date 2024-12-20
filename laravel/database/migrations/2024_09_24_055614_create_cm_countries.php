<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cm_lib_countries', function (Blueprint $table) {
            $table->id('lco_id');
            $table->string('lco_name', 100)->index();
            $table->string('lco_code', 6)->index();
            $table->float('lco_coord_latitude')->index();
            $table->float('lco_coord_longitude')->index();
            $table->integer('lco_currency_id');
            $table->integer('lco_lang_id');
            $table->integer('lco_weight_id');
            $table->integer('lco_length_id');
            $table->timestamps();
        });

        Schema::create('cm_lib_cities', function (Blueprint $table) {
            $table->id('lcit_id');
            $table->string('lcit_name', 50)->index();
            $table->float('lcit_coord_latitude')->index();
            $table->float('lcit_coord_longitude')->index();
            $table->timestamps();
        });

        Schema::create('cm_lib_languages', function (Blueprint $table) {
            $table->id('llang_id');
            $table->string('llang_name', 30)->index();
            $table->string('llang_code', 6)->index();
            $table->timestamps();
        });

        Schema::create('cm_lib_currencies', function (Blueprint $table) {
            $table->id('lcur_id');
            $table->string('lcur_name', 30)->index();
            $table->string('lcur_code', 6)->index();
            $table->string('lcur_symbol', 5)->index();
            $table->timestamps();
        });

        Schema::table('cm_chain_restaurants', function (Blueprint $table) {
            $table->integer('cr_country_id')->after('cr_id')->index();
        });

        Schema::table('cm_restaurants', function (Blueprint $table) {
            $table->integer('r_chain_id')->index();
            $table->integer('r_city_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_lib_countries');
        Schema::dropIfExists('cm_lib_cities');
        Schema::dropIfExists('cm_lib_languages');
        Schema::dropIfExists('cm_lib_currencies');
    }
};
