<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_order_ds', function (Blueprint $table) {
            $table->string('ods_ds_pp_id', 100)->nullable()->after('ods_ds_id');
        });
    }

    public function down(): void
    {
        Schema::table('rw_order_ds', function (Blueprint $table) {
            $table->dropColumn('ods_ds_pp_id');
        });
    }
};
