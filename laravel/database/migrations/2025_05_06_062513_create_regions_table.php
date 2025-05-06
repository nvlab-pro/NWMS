<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_lib_regions', function (Blueprint $table) {
            $table->increments('lr_id');
            $table->string('lr_name'); // Например: "Ленинградская область"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_lib_regions');
    }
};
