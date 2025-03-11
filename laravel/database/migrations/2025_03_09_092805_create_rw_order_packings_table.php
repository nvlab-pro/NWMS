<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_order_packings', function (Blueprint $table) {
            $table->id('op_id');
            $table->unsignedBigInteger('op_order_id');
            $table->unsignedBigInteger('op_offer_id');
            $table->unsignedBigInteger('op_user_id');
            $table->string('op_barcode', 30);
            $table->dateTime('op_data');
            $table->float('op_qty');
            $table->integer('op_cash');
            $table->timestamps();

            $table->index('op_order_id');
            $table->index('op_offer_id');
            $table->index('op_user_id');
            $table->index('op_cash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rw_order_packings');
    }

};
