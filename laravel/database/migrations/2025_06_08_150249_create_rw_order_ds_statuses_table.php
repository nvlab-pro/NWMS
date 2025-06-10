<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_order_ds_statuses', function (Blueprint $table) {
            $table->bigIncrements('odss_id')->unsigned();
            $table->string('odss_name', 25);
            $table->string('odss_color', 7);
            $table->string('odss_bgcolor', 7);
        });

        // Наполняем таблицу начальными данными
        DB::table('rw_order_ds_statuses')->insert([
            [
                'odss_id' => 100,
                'odss_name' => 'Принято службой доставки',
                'odss_color' => '#FFFFFF',
                'odss_bgcolor' => '#eac700',
            ],
            [
                'odss_id' => 200,
                'odss_name' => 'Доставляется',
                'odss_color' => '#FFFFFF',
                'odss_bgcolor' => '#bde800',
            ],
            [
                'odss_id' => 300,
                'odss_name' => 'Доставлено в ПВЗ',
                'odss_color' => '#FFFFFF',
                'odss_bgcolor' => '#0048ff',
            ],
            [
                'odss_id' => 400,
                'odss_name' => 'Доставлено',
                'odss_color' => '#FFFFFF',
                'odss_bgcolor' => '#008728',
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_order_ds_statuses');
    }
};
