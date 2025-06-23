<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_billing_settings', function (Blueprint $table) {
            $table->bigIncrements('bs_id');
            $table->unsignedBigInteger('bs_domain_id');
            $table->date('bs_data');
            $table->integer('bs_status')->default(1);
            $table->string('bs_name', 50);
            $table->text('bs_tariffs')->nullable();
            $table->timestamps(); // Добавим created_at и updated_at (можно убрать если не нужно)

            // Индексы
            $table->index('bs_domain_id');
            $table->index('bs_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_billing_settings');
    }
};
