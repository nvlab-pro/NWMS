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
        Schema::table('rw_orders', function (Blueprint $table) {
            $table->integer('o_current_pallet')->default(1)->after('o_order_place');
            $table->integer('o_current_box')->default(1)->after('o_current_pallet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rw_orders', function (Blueprint $table) {
            $table->integer('o_current_pallet')->default(1)->after('o_order_place');
            $table->integer('o_current_box')->default(1)->after('o_current_pallet');
        });
    }
};
