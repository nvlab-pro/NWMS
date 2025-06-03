<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->unsignedBigInteger('wh_company_id')
                ->nullable()
                ->after('wh_country_id');
        });
    }

    public function down(): void
    {
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->dropColumn('wh_company_id');
        });
    }
};
