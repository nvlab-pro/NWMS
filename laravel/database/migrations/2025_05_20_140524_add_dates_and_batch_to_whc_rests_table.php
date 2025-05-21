<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whc_rests', function (Blueprint $table) {
            $table->date('whcr_production_date')->nullable()->index()->after('whcr_date');
            $table->date('whcr_expiration_date')->nullable()->index()->after('whcr_production_date');
            $table->string('whcr_batch', 15)->nullable()->index()->after('whcr_expiration_date');
        });
    }

    public function down(): void
    {
        Schema::table('whc_rests', function (Blueprint $table) {
            $table->dropColumn(['whcr_production_date', 'whcr_expiration_date', 'whcr_batch']);
        });
    }
};
