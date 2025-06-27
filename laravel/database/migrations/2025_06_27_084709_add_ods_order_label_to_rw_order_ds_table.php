<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_order_ds', function (Blueprint $table) {
            $table->string('ods_order_label', 150)
                ->nullable()
                ->after('ods_order_ds_id');
        });
    }

    public function down(): void
    {
        Schema::table('rw_order_ds', function (Blueprint $table) {
            $table->dropColumn('ods_order_label');
        });
    }
};
