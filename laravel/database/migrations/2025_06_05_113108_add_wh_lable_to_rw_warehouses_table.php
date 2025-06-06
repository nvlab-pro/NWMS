<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->text('wh_custom_label')->nullable()->after('wh_set_production_date'); // или другое подходящее поле
            $table->integer('wh_use_custom_label')->nullable()->after('wh_custom_label');
        });
    }

    public function down(): void
    {
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->dropColumn('wh_custom_label');
            $table->dropColumn('wh_use_custom_label');
        });
    }
};
