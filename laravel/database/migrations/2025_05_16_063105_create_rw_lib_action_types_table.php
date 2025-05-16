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
        Schema::create('rw_lib_action_types', function (Blueprint $table) {
            $table->increments('lat_id');              // PK: автоинкрементный ID
            $table->string('lat_code')->unique();      // Уникальный символьный код действия
            $table->string('lat_name');               // Название действия для отображения
            $table->timestamps();                     // created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rw_lib_action_types');
    }
};
