<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_orders', function (Blueprint $table) {
            $table->float('o_dimension_x')->default(0)->after('o_current_box');
            $table->float('o_dimension_y')->default(0)->after('o_dimension_x');
            $table->float('o_dimension_z')->default(0)->after('o_dimension_y');
            $table->float('o_weight')->default(0)->after('o_dimension_z');
        });
    }

    public function down(): void
    {
        Schema::table('rw_orders', function (Blueprint $table) {
            $table->dropColumn([
                'o_dimension_x',
                'o_dimension_y',
                'o_dimension_z',
                'o_weight',
            ]);
        });
    }
};
