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
        Schema::table('rw_order_packings', function (Blueprint $table) {
            $table->integer('op_pallet')->default(1)->after('op_qty');
            $table->integer('op_box')->default(1)->after('op_pallet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rw_order_packings', function (Blueprint $table) {
            $table->dropColumn(['op_pallet', 'op_box']);
        });
    }
};
