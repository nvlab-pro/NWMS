<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wh_employees_attendances_rests', function (Blueprint $table) {
            $table->id('ear_id');
            $table->tinyInteger('ear_type')->comment('0 - пропуск / 1 - отпросился / 2 - отпуск / 3 - больничный');
            $table->date('ear_date_from');
            $table->date('ear_date_to');
            $table->unsignedBigInteger('ear_user_id');
            $table->string('ear_comment', 255)->nullable();
            $table->timestamps();

            $table->index('ear_type');
            $table->index('ear_date_from');
            $table->index('ear_date_to');
            $table->index('ear_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wh_employees_attendances_rests');
    }
};
