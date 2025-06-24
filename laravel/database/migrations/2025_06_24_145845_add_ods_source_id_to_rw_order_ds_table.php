<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_order_ds', function (Blueprint $table) {
            $table->bigInteger('ods_source_id')->nullable()->after('ods_track_number');
        });
    }

    public function down(): void
    {
        Schema::table('rw_order_ds', function (Blueprint $table) {
            $table->dropColumn('ods_source_id');
        });
    }
};
