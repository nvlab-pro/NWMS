<?php

use App\Jobs\CalculateBillingJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\ReserveOrdersJob;
use App\Jobs\UpdateYandexPickups;

/*
|--------------------------------------------------------------------------
| Определяем команды (Command Bus)
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('orders:reserve', function () {
    ReserveOrdersJob::dispatch();
})->purpose('Reserve awaiting orders');

Artisan::command('delivery:yd-pickups:update', function () {
    UpdateYandexPickups::dispatch();
})->purpose('Update Yandex Delivery pickup points');

Artisan::command('billing:calculate', function () {
    dispatch(new CalculateBillingJob)->onQueue('billing');
})->purpose('Manual run of billing calculation');

/*
|--------------------------------------------------------------------------
| Планировщик (Scheduler)
|--------------------------------------------------------------------------
*/

Schedule::command('inspire')
    ->hourly();

Schedule::command('orders:reserve')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('delivery:yd-pickups:update')
    ->everyTwoHours()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('billing:calculate')
    ->everyFourHours()
    ->withoutOverlapping()
    ->runInBackground();