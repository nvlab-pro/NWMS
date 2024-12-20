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
        Schema::create('cm_chain_restaurants', function (Blueprint $table) {
            $table->id('cr_id')->autoIncrement();
            $table->string('cr_name', 100)->index();
            $table->text('cr_description')->nullable();;
            $table->timestamps();
        });

        Schema::create('cm_restaurants', function (Blueprint $table) {
            $table->id('r_id')->autoIncrement();
            $table->string('r_logo');
            $table->string('r_name', 100);
            $table->text('r_description')->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_chain_restaurants');
        Schema::dropIfExists('cm_restaurants');
    }
};
