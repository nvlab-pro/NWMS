<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->float('wh_billing_cost')->nullable()->after('wh_use_custom_label');
            $table->float('wh_billing_received')->nullable()->after('wh_billing_cost');
            $table->float('wh_billing_sum')->nullable()->after('wh_billing_received');
        });
    }

    public function down(): void
    {
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->dropColumn('wh_billing_cost');
            $table->dropColumn('wh_billing_received');
            $table->dropColumn('wh_billing_sum');
        });
    }
};
