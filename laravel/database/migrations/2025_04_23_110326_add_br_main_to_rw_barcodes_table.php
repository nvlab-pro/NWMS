<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rw_barcodes', function (Blueprint $table) {
            $table->tinyInteger('br_main')
                ->nullable()
                ->after('br_shop_id')
                ->index()
                ->comment('Основной штрихкод');
        });
    }

    public function down()
    {
        Schema::table('rw_barcodes', function (Blueprint $table) {
            $table->dropIndex(['br_main']);
            $table->dropColumn('br_main');
        });
    }
};
