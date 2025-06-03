<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_order_contacts', function (Blueprint $table) {
            $table->id('oc_id');
            $table->bigInteger('oc_order_id')->index();
            $table->string('oc_first_name', 50)->nullable();
            $table->string('oc_middle_name', 50)->nullable();
            $table->string('oc_last_name', 50)->nullable();
            $table->bigInteger('oc_phone')->nullable()->index();
            $table->string('oc_email', 75)->nullable();
            $table->bigInteger('oc_country_id')->nullable()->index();
            $table->bigInteger('oc_city_id')->nullable()->index();
            $table->string('oc_postcode', 10)->nullable()->index();
            $table->double('oc_coord_latitude', 12, 10)->nullable()->index();
            $table->double('oc_coord_longitude', 13, 10)->nullable()->index();
            $table->string('oc_full_address', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_order_contacts');
    }
};
