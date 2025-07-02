<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateBillingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // ► НЕ определяем собственное $queue - иначе коллизия c Queueable
//    public $queue = 'billing';          // ← достаточно указать публичное свойство

    public function handle(): void
    {
        Log::info('[CalculateBilling] started at ' . now());

        dump('OK!');

        // … вся логика расчёта …
    }
}