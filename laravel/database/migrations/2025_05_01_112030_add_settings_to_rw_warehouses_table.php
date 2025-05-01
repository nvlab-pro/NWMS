<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->integer('wh_set_expiration_date')->default(0)->after('wh_name');
            $table->integer('wh_set_batch')->default(0)->after('wh_set_expiration_date');
            $table->integer('wh_set_production_date')->default(0)->after('wh_set_batch');
        });
    }

    public function down(): void
    {
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->dropColumn([
                'wh_set_expiration_date',
                'wh_set_batch',
                'wh_set_production_date',
            ]);
        });
    }
};
