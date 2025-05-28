<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_print_templates', function (Blueprint $table) {
            $table->bigIncrements('pt_id');
            $table->unsignedBigInteger('pt_domain_id')->index();
            $table->unsignedBigInteger('pt_user_id')->nullable()->index();
            $table->string('pt_name', 50);
            $table->string('pt_modul', 15)->index();
            $table->unsignedBigInteger('pt_attachment_id')->index();
            $table->string('pt_type', 5)->index();
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_print_templates');
    }
};
