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
        Schema::table('cm_lib_cities', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('cm_lib_countries', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('cm_lib_currencies', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('cm_lib_languages', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('cm_lib_lengths', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('cm_lib_weights', function (Blueprint $table) {
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cm_lib_cities', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cm_lib_countries', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cm_lib_currencies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cm_lib_languages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cm_lib_lengths', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cm_lib_weights', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

    }
};
