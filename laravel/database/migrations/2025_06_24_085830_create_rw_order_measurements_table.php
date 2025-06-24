<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_order_measurements', function (Blueprint $table) {
            $table->id('om_id');
            $table->integer('om_x')->unsigned()->default(0);
            $table->integer('om_y')->unsigned()->default(0);
            $table->integer('om_z')->unsigned()->default(0);
            $table->integer('om_weight')->unsigned()->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_order_measurements');
    }
};
