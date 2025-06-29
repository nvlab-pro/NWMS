<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_user_actions', function (Blueprint $table) {
            $table->string('ua_barcode', 30)->nullable()->after('ua_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('rw_user_actions', function (Blueprint $table) {
            $table->dropColumn('ua_barcode');
        });
    }
};
