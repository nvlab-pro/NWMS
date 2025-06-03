<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rw_companies', function (Blueprint $table) {
            $table->bigIncrements('co_id');                      // Primary Key
            $table->unsignedBigInteger('co_domain_id')->nullable()->index(); // ID домена
            $table->string('co_name', 150);                      // Название компании
            $table->string('co_legal_name', 150)->nullable();    // Юридическое название (если отличается)
            $table->string('co_vat_number', 30)->nullable()->index(); // НДС / ИНН / Tax ID
            $table->string('co_registration_number', 30)->nullable(); // Регистрационный номер (ОГРН, UIC, EIN и т.д.)
            $table->unsignedBigInteger('co_country_id')->nullable()->index(); // ID старны
            $table->unsignedBigInteger('co_city_id')->nullable();          // Город
            $table->string('co_postcode', 20)->nullable()->index(); // Почтовый индекс
            $table->string('co_address', 255)->nullable();       // Адрес
            $table->string('co_phone', 30)->nullable();          // Телефон
            $table->string('co_email', 100)->nullable();         // Email
            $table->string('co_website', 100)->nullable();       // Сайт
            $table->string('co_bank_account', 50)->nullable();   // Расчётный счёт / IBAN
            $table->string('co_bank_ks', 50)->nullable();   // Кор.счет
            $table->string('co_bank_name', 100)->nullable();     // Название банка
            $table->string('co_swift_bic', 20)->nullable();      // SWIFT / BIC
            $table->string('co_contact_person', 100)->nullable(); // Контактное лицо

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rw_companies');
    }
};
