<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Добавляем поля в таблицу rw_warehouses
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->integer('wh_status')->unsigned()->default(1)->index()->after('wh_id');
            $table->bigInteger('wh_billing_id')->unsigned()->default(0)->after('wh_company_id');
        });

        // Создаем новую таблицу rw_wh_billings
        Schema::create('rw_wh_billings', function (Blueprint $table) {
            $table->bigIncrements('wb_id');
            $table->dateTime('wb_date');
            $table->bigInteger('wb_wh_id')->unsigned()->index();
            $table->bigInteger('wb_billing_id')->unsigned()->index();
            $table->string('wb_comment', 150)->nullable();
            $table->timestamps(); // если нужно хранить created_at, updated_at
        });
    }

    public function down()
    {
        // Откат изменений в rw_warehouses
        Schema::table('rw_warehouses', function (Blueprint $table) {
            $table->dropColumn(['wh_status', 'wh_billing_id']);
        });

        // Удаляем таблицу rw_wh_billings
        Schema::dropIfExists('rw_wh_billings');
    }
};
