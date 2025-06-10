<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_order_ds', function (Blueprint $table) {
            $table->bigIncrements('ods_id')->unsigned();
            $table->unsignedBigInteger('ods_ds_id')->index();
            $table->unsignedInteger('ods_status')->nullable()->index();
            $table->string('ods_track_number', 50)->nullable()->index();

            $table->timestamps(); // Добавим created_at и updated_at (можно убрать если не нужно)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_order_ds');
    }
};
