<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_offers', function (Blueprint $table) {
            $table->tinyInteger('of_datamatrix')->nullable()->index()->after('of_weight');
            $table->dropColumn('of_datamarix');
        });
    }

    public function down(): void
    {
        Schema::table('rw_offers', function (Blueprint $table) {
            $table->dropIndex(['of_datamatrix']);
            $table->dropColumn('of_datamatrix');
        });
    }
};
