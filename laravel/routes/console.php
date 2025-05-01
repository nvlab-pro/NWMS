<?php

use App\Console\Integrations\scheduleYandexDelivery;
use App\Console\scheduleOrders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('reserveOrders', function () {
    $current = new scheduleOrders();
    $current->reserveOrders();
})->everyFiveMinutes();

Artisan::command('updateYDPickUps', function () {
    $current = new scheduleYandexDelivery();
    $current->getPickUpsList();
})->everyTwoHours();
