<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_billing_transactions', function (Blueprint $table) {
            $table->id('bt_id');
            $table->dateTime('bt_date')->index();
            $table->string('bt_service', 20)->index();
            $table->unsignedBigInteger('bt_shop_id')->index();
            $table->unsignedBigInteger('bt_billing_id')->index();
            $table->unsignedBigInteger('bt_doc_id')->index();
            $table->unsignedInteger('bt_entity_count');
            $table->float('bt_sum')->default(0);
            $table->unsignedBigInteger('bt_act_id')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_billing_transactions');
    }
};
