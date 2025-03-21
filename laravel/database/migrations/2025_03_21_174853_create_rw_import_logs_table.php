<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_import_logs', function (Blueprint $table) {
            $table->id('il_id');
            $table->integer('il_import_id')->index();
            $table->dateTime('il_date')->index();
            $table->tinyInteger('il_operation')->index();
            $table->string('il_name');
            $table->json('il_fields');
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_import_logs');
    }
};

