<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_integrations', function (Blueprint $table) {
            $table->integer('int_status')->default(1)->index()->after('int_id');
        });
    }

    public function down(): void
    {
        Schema::table('rw_integrations', function (Blueprint $table) {
            $table->dropColumn('int_status');
        });
    }
};
