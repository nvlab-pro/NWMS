<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_pickup_points', function (Blueprint $table) {
            $table->integer('pp_status')->index()->after('pp_id');
            $table->integer('pp_update')->index()->after('pp_status');
        });
    }

    public function down(): void
    {
        Schema::table('rw_pickup_points', function (Blueprint $table) {
            $table->dropIndex(['pp_status']);
            $table->dropColumn('pp_status');

            $table->dropIndex(['pp_update']);
            $table->dropColumn('pp_update');
        });
    }
};
