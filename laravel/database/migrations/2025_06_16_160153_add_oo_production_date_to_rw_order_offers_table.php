<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_order_offers', function (Blueprint $table) {
            $table->date('oo_production_date')->nullable()->index()->after('oo_price');
        });
    }

    public function down(): void
    {
        Schema::table('rw_order_offers', function (Blueprint $table) {
            $table->dropColumn('oo_production_date');
        });
    }
};
