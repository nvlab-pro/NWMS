<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_billing_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('bs_date_type')->default(0)->after('bs_status');
        });
    }

    public function down(): void
    {
        Schema::table('rw_billing_settings', function (Blueprint $table) {
            $table->dropColumn('bs_date_type');
        });
    }
};
