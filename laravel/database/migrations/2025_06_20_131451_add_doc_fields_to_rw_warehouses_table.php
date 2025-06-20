<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->string('wh_doc_num', 10)->nullable()->after('wh_company_id');
            $table->string('wh_doc_date', 15)->nullable()->after('wh_doc_num');
        });
    }

    public function down(): void
    {
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->dropColumn(['wh_doc_num', 'wh_doc_date']);
        });
    }
};
