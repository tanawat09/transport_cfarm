<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('app:about', function () {
    $this->info('ระบบบริหารรถขนส่งอาหารไก่');
})->purpose('Display basic application information.');
