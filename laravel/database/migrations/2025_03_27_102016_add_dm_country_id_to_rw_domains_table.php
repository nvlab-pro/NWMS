<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rw_domains', function (Blueprint $table) {
            $table->integer('dm_country_id')->after('dm_name')->index();
        });
    }

    public function down()
    {
        Schema::table('rw_domains', function (Blueprint $table) {
            $table->dropIndex(['dm_country_id']);
            $table->dropColumn('dm_country_id');
        });
    }
};
