<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw_user_actions', function (Blueprint $table) {
            $table->unsignedBigInteger('ua_doc_id')
                ->nullable()
                ->after('ua_entity_type')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('rw_user_actions', function (Blueprint $table) {
            $table->dropIndex(['ua_doc_id']);
            $table->dropColumn('ua_doc_id');
        });
    }
};
