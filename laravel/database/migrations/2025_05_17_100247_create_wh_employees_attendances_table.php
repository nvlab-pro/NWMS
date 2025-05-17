<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wh_employees_attendances', function (Blueprint $table) {
            $table->id('ea_id');
            $table->unsignedBigInteger('ea_user_id');
            $table->dateTime('ea_date');
            $table->tinyInteger('ea_type');
            $table->timestamps();

            $table->index('ea_user_id');
            $table->index('ea_date');
            $table->index('ea_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wh_employees_attendances');
    }
};
