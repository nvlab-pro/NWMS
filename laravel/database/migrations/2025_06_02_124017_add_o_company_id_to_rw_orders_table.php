<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('o_company_id')->nullable()->index()->after('o_parcel_id');
        });
    }

    public function down(): void
    {
        Schema::table('rw_orders', function (Blueprint $table) {
            $table->dropColumn('o_company_id');
        });
    }
};
