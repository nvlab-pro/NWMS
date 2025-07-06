<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_invoces', function (Blueprint $table) {
            $table->id('in_id');
            $table->integer('in_status')->default(0)->index();
            $table->date('in_date')->nullable();
            $table->unsignedBigInteger('in_wh_id')->index();
            $table->unsignedBigInteger('in_customer_company_id');
            $table->unsignedBigInteger('in_executor_company_id');
            $table->float('in_sum');
            $table->float('in_tax');
            $table->float('in_total_sum');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_invoces');
    }
};
