<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_integrations', function (Blueprint $table) {
            $table->string('int_pickup_point', 150)->nullable()->after('int_token');
        });
    }

    public function down(): void
    {
        Schema::table('rw_integrations', function (Blueprint $table) {
            $table->dropColumn('int_pickup_point');
        });
    }
};
