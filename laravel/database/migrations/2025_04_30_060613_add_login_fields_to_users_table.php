<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_domains', function (Blueprint $table) {
            $table->string('dm_timezone', 64)->nullable()->after('dm_country_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('rw_domains', function (Blueprint $table) {
            $table->dropColumn('dm_timezone');
        });
    }
};
