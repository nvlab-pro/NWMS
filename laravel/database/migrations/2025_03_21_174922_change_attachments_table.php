<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->tinyInteger('status')->nullable()->after('id')->index();
            $table->string('type', 10)->nullable()->after('status')->index();
            $table->integer('domain_id')->nullable()->after('type')->index();
            $table->tinyInteger('import_type')->nullable()->after('domain_id')->index();

            // Добавим индексы на существующие поля
            $table->index('group');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn(['status', 'type', 'domain_id', 'import_type']);
            $table->dropIndex(['group']);
            $table->dropIndex(['user_id']);
        });
    }
};
