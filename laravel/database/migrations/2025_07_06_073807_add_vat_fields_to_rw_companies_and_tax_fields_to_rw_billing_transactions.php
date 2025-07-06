<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Таблица rw_companies
        Schema::table('rw_companies', function (Blueprint $table) {
            $table->tinyInteger('co_vat_availability')->default(0)->after('co_vat_number');
            $table->tinyInteger('co_vat_proc')->default(0)->after('co_vat_availability');
        });

        // Таблица rw_billing_transactions
        Schema::table('rw_billing_transactions', function (Blueprint $table) {
            $table->float('bt_tax')->default(0)->after('bt_sum');
            $table->float('bt_total_sum')->default(0)->after('bt_tax');
        });
    }

    public function down(): void
    {
        Schema::table('rw_companies', function (Blueprint $table) {
            $table->dropColumn(['co_vat_availability', 'co_vat_proc']);
        });

        Schema::table('rw_billing_transactions', function (Blueprint $table) {
            $table->dropColumn(['bt_tax', 'bt_total_sum']);
        });
    }
};
