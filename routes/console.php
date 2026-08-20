<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automated Abandoned Cart recovery scan every 15 minutes
\Illuminate\Support\Facades\Schedule::command('carts:process-abandoned')
    ->everyFifteenMinutes()
    ->withoutOverlapping();
