<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_orders', function (Blueprint $table) {
            $table->unsignedInteger('o_customer_type')
                ->nullable()
                ->index()
                ->after('o_company_id');
        });
    }

    public function down(): void
    {
        Schema::table('rw_orders', function (Blueprint $table) {
            $table->dropColumn('o_customer_type');
        });
    }
};
