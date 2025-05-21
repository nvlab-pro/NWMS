<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whc_rests', function (Blueprint $table) {
            $table->tinyInteger('whcr_updated')->nullable()->after('whcr_batch');
            $table->index('whcr_updated');
        });
    }

    public function down(): void
    {
        Schema::table('whc_rests', function (Blueprint $table) {
            $table->dropIndex(['whcr_updated']);
            $table->dropColumn('whcr_updated');
        });
    }
};
