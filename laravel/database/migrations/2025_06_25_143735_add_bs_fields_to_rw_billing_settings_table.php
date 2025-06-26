<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_billing_settings', function (Blueprint $table) {
            $table->string('bs_fields', 500)->nullable()->after('bs_tariffs');
        });
    }

    public function down(): void
    {
        Schema::table('rw_billing_settings', function (Blueprint $table) {
            $table->dropColumn('bs_fields');
        });
    }
};
