<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_datamatrix', function (Blueprint $table) {
            $table->unsignedBigInteger('dmt_acceptance_id')
                ->nullable()
                ->after('dmt_shop_id')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('rw_datamatrix', function (Blueprint $table) {
            $table->dropIndex(['dmt_acceptance_id']);
            $table->dropColumn('dmt_acceptance_id');
        });
    }
};
