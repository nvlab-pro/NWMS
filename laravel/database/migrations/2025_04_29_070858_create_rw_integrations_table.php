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
        Schema::create('rw_integrations', function (Blueprint $table) {
            $table->id('int_id');
            $table->unsignedInteger('int_domain_id')->index();
            $table->unsignedInteger('int_user_id')->index();
            $table->unsignedInteger('int_type')->nullable()->index();
            $table->unsignedInteger('int_ds_id')->index();
            $table->string('int_name', 50);
            $table->string('int_url', 150);
            $table->string('int_token', 250);
            $table->timestamps(); // если нужно created_at / updated_at, можно убрать если не требуется
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rw_integrations');
    }
};
