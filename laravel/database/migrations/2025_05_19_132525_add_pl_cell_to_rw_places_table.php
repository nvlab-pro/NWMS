<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_places', function (Blueprint $table) {
            $table->tinyInteger('pl_cell')->nullable()->index()->after('pl_shelf');
        });
    }

    public function down(): void
    {
        Schema::table('rw_places', function (Blueprint $table) {
            $table->dropIndex(['pl_cell']);
            $table->dropColumn('pl_cell');
        });
    }
};
