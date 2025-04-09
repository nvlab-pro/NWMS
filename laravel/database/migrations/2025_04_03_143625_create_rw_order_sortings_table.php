<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_order_sortings', function (Blueprint $table) {
            $table->id('os_id');
            $table->unsignedBigInteger('os_user_id');
            $table->unsignedBigInteger('os_order_id');
            $table->unsignedBigInteger('os_offer_id');
            $table->unsignedBigInteger('os_place_id');
            $table->string('os_barcode', 30);
            $table->float('os_qty');
            $table->dateTime('os_data');
            $table->integer('os_cash');
            $table->timestamps();

            $table->index('os_user_id');
            $table->index('os_order_id');
            $table->index('os_offer_id');
            $table->index('os_place_id');
            $table->index('os_cash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_order_sortings');
    }
};
