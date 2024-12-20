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
        //
        Schema::table('cm_chain_restaurants', function (Blueprint $table) {
            $table->softDeletes(); // добавляем столбец lcit_country_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('cm_chain_restaurants', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
