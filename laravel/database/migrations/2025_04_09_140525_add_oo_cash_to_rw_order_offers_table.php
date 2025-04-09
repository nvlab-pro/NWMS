<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_order_offers', function (Blueprint $table) {
            $table->integer('oo_cash')
                ->nullable()
                ->index()
                ->after('oo_operation_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('rw_order_offers', function (Blueprint $table) {
            $table->dropIndex(['oo_cash']);
            $table->dropColumn('oo_cash');
        });
    }
};
