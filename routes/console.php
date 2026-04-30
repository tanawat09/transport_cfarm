<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('app:about', function () {
    $this->info('ระบบบริหารรถขนส่งอาหารไก่');
})->purpose('Display basic application information.');

Schedule::command('vehicle-documents:notify-expiring')->dailyAt('08:00');
Schedule::command('tire-registrations:notify-replacement-alerts')->dailyAt('08:15');
