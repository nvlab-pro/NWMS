<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_order_ds', function (Blueprint $table) {
            $table->string('ods_order_ds_id', 100)
                ->nullable()
                ->after('ods_source_id')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('rw_order_ds', function (Blueprint $table) {
            $table->dropIndex(['ods_order_ds_id']);
            $table->dropColumn('ods_order_ds_id');
        });
    }
};
