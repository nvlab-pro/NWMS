<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('rw_settings_proc_packings', function (Blueprint $table) {
            $table->id('spp_id');
            $table->unsignedInteger('spp_status_id');
            $table->unsignedInteger('spp_priority');
            $table->unsignedInteger('spp_domain_id');
            $table->unsignedBigInteger('spp_wh_id');
            $table->unsignedBigInteger('spp_user_id');
            $table->string('spp_name', 150);
            $table->unsignedInteger('spp_start_place_type')->nullable();
            $table->unsignedInteger('spp_place_rack_from')->nullable();
            $table->unsignedInteger('spp_place_rack_to')->nullable();
            $table->unsignedTinyInteger('spp_packing_type')->nullable();
            $table->unsignedInteger('spp_ds_id')->nullable();
            $table->timestamps();

            // Индексы
            $table->index('spp_status_id');
            $table->index('spp_priority');
            $table->index('spp_domain_id');
            $table->index('spp_wh_id');
            $table->index('spp_user_id');
            $table->index('spp_ds_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rw_settings_proc_packings');
    }
};
