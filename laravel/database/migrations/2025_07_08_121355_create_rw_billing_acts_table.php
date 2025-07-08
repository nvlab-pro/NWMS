<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_billing_acts', function (Blueprint $table) {
            $table->id('ba_id');
            $table->unsignedBigInteger('ba_wh_id')->index();
            $table->integer('ba_status')->index()->default(0);
            $table->date('ba_date_start')->index();
            $table->date('ba_date_end')->index();
            $table->unsignedBigInteger('ba_customer_company_id')->index();
            $table->unsignedBigInteger('ba_executor_company_id')->index();
            $table->float('ba_sum')->default(0);
            $table->float('ba_tax_sum')->default(0);
            $table->float('ba_sum_total')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_billing_acts');
    }
};
