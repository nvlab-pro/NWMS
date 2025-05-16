<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rw_user_actions', function (Blueprint $table) {
            $table->increments('ua_id');               // PK: автоинкрементный ID действия
            $table->unsignedBigInteger('ua_user_id');  // ID пользователя (кладовщика)
            $table->unsignedInteger('ua_lat_id');      // ID типа действия (foreign key на rw_lib_action_types)
            $table->unsignedInteger('ua_domain_id');   // ID домена
            $table->unsignedInteger('ua_wh_id');       // ID склада (warehouse)
            $table->unsignedInteger('ua_shop_id')->nullable();   // ID магазина (может быть NULL)
            $table->unsignedInteger('ua_place_id')->nullable();  // ID ячейки/места (может быть NULL)
            $table->string('ua_entity_type');          // Тип сущности ("offer", "order", и т.д.)
            $table->unsignedInteger('ua_entity_id');   // ID сущности данного типа
            $table->float('ua_quantity');              // Количество (поддерживает дробные значения)
            $table->timestamp('ua_time_start');        // Время начала действия
            $table->timestamp('ua_time_end')->nullable();   // Время окончания действия (NULL, если не завершено или моментальное)
            $table->unsignedInteger('ua_transaction_id')->nullable(); // ID транзакции (если применимо)
            $table->timestamps();                     // created_at и updated_at
            $table->foreign('ua_lat_id')->references('lat_id')->on('rw_lib_action_types');
            $table->foreign('ua_user_id')->references('id')->on('users'); // если есть таблица пользователей
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rw_user_actions');
    }
};
