<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_settings_markings', function (Blueprint $table) {
            $table->bigIncrements('sm_id');

            $table->unsignedInteger('sm_status_id')->index();
            $table->integer('sm_priority')->index();
            $table->unsignedInteger('sm_domain_id')->index();
            $table->unsignedBigInteger('sm_user_id')->index();
            $table->string('sm_name', 150);
            $table->unsignedBigInteger('sm_ds_id')->index();

            $table->date('sm_date_from')->nullable();
            $table->date('sm_date_to')->nullable();

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_settings_markings');
    }
};
