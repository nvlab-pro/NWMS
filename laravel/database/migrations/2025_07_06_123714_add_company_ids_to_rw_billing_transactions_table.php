<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_billing_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('bt_customer_company_id')->nullable()->index()->after('bt_billing_id');
            $table->unsignedBigInteger('bt_executor_company_id')->nullable()->index()->after('bt_customer_company_id');
        });
    }

    public function down(): void
    {
        Schema::table('rw_billing_transactions', function (Blueprint $table) {
            $table->dropColumn(['bt_customer_company_id', 'bt_executor_company_id']);
        });
    }
};
