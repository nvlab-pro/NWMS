<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_lib_action_types', function (Blueprint $table) {
            $table->string('lat_color', 7)->nullable()->after('lat_name');
            $table->string('lat_bgcolor', 7)->nullable()->after('lat_color');
        });
    }

    public function down(): void
    {
        Schema::table('rw_lib_action_types', function (Blueprint $table) {
            $table->dropColumn(['lat_color', 'lat_bgcolor']);
        });
    }
};
