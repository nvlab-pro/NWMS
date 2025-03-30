<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_datamatrix', function (Blueprint $table) {
            $table->id('dmt_id'); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY
            $table->tinyInteger('dmt_status')->unsigned()->index();
            $table->integer('dmt_shop_id')->unsigned()->index();
            $table->bigInteger('dmt_order_id')->unsigned()->nullable()->index(); // исправлено
            $table->string('dmt_barcode', 14)->index();
            $table->string('dmt_short_code', 14)->index();
            $table->string('dmt_crypto_tail', 90)->index();
            $table->string('dmt_datamatrix', 125)->unique()->index();
            $table->date('dmt_created_date')->nullable(); // разрешаем null
            $table->date('dmt_used_date')->nullable();    // разрешаем null
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_datamatrix');
    }
};