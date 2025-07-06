<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_billing_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('bt_wh_id')->index()->after('bt_shop_id');
            $table->string('bt_desc', 150)->nullable()->after('bt_act_id');
            $table->timestamps(); // Добавим created_at и updated_at (можно убрать если не нужно)
        });
    }

    public function down(): void
    {
        Schema::table('rw_billing_transactions', function (Blueprint $table) {
            $table->dropColumn('bt_desc');
        });
    }
};
