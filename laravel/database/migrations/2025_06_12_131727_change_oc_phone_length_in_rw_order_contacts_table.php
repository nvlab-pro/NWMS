<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_order_contacts', function (Blueprint $table) {
            $table->string('oc_phone', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('rw_order_contacts', function (Blueprint $table) {
            $table->string('oc_phone', 20)->change();
        });
    }
};
