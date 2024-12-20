<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cm_lib_lengths', function (Blueprint $table) {
            $table->id('llen_id');
            $table->string('llen_name', 50);
            $table->string('llen_unit', 6);
            $table->timestamps();
        });

        Schema::create('cm_lib_weights', function (Blueprint $table) {
            $table->id('lw_id');
            $table->string('lw_name', 50);
            $table->string('lw_unit', 6);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_lib_length');
        Schema::dropIfExists('cm_lib_weight');
    }
};
