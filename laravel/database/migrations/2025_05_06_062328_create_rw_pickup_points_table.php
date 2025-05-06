<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_pickup_points', function (Blueprint $table) {
            $table->bigIncrements('pp_id');
            $table->string('pp_ext_id', 50)->index();
            $table->string('pp_station_id', 25)->index();
            $table->string('pp_name', 150);
            $table->string('pp_type', 20)->index();
            $table->float('pp_position_latitude', 12, 9)->index();
            $table->float('pp_position_longitude', 12, 9)->index();
            $table->integer('pp_geoId')->index();
            $table->integer('pp_country_id')->index();
            $table->integer('pp_region_id')->index();
            $table->integer('pp_city_id')->index();
            $table->string('pp_street', 50)->index();
            $table->string('pp_house', 15)->index();
            $table->string('pp_apartment', 6)->index();
            $table->string('pp_building', 6)->index();
            $table->string('pp_postal_code', 10)->index();
            $table->string('pp_full_address', 150);
            $table->tinyInteger('pp_payed')->index();
            $table->string('pp_phone', 15);
            $table->text('pp_schedule');
            $table->text('pp_comment');
            $table->text('pp_instruction');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_pickup_points');
    }
};
