<?php

namespace App\Orchid\Services;

class ChestnyZnakParser
{
    protected string $raw;
    protected ?string $gtin = null;
    protected ?string $serial = null;
    protected ?string $crypto = null;

    public function __construct(string $datamatrix)
    {
        $this->raw = preg_replace('/\s+/', '', $datamatrix); // Убираем пробелы/переводы строк
        $this->parse();
    }

    protected function parse(): void
    {
        // Проверка структуры — ищем 01(GTIN)21(SN)[91...]
        if (preg_match('/^01(\d{14})21(.+?)(91(.+))?$/', $this->raw, $matches)) {
            $this->gtin = $matches[1] ?? null;
            $this->serial = $matches[2] ?? null;
            $this->crypto = $matches[4] ?? null;
        }
    }


    /**
     * Полный код
     */
    public function getFullCode(): ?string
    {
        return $this->raw;
    }

    /**
     * Проверка, что код является валидным кодом Честного знака
     */
    public function isValid(): bool
    {
        return $this->gtin !== null && $this->serial !== null;
    }

    /**
     * Получить GTIN / EAN-13 (обрезаем 14-й ведущий ноль)
     */
    public function getEAN13(): ?string
    {
        if (!$this->gtin) return null;

        // Если GTIN начинается с нуля и 14 символов — убираем ведущий
        return ltrim($this->gtin, '0');
    }

    /**
     * Получить короткий код (Код идентификации / SN), который подходят для маркетплейсов
     */
    public function getShortCode(): ?string
    {
        return $this->serial;
    }

    /**
     * Получить криптохвост (если нужен)
     */
    public function getCryptoTail(): ?string
    {
        return $this->crypto;
    }
}