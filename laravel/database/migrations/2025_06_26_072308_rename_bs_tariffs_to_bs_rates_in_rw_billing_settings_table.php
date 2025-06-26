<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_billing_settings', function (Blueprint $table) {
            $table->renameColumn('bs_tariffs', 'bs_rates');
        });
    }

    public function down(): void
    {
        Schema::table('rw_billing_settings', function (Blueprint $table) {
            $table->renameColumn('bs_rates', 'bs_tariffs');
        });
    }
};
