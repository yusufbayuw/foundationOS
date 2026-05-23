<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('fos:moodle:drain-outbox')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('fos:moodle:reconcile all --dry-run --limit=500')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('fos:library:recalc-fines')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('fos:billing:check-grace-period')
    ->dailyAt('01:00')
    ->withoutOverlapping();

Schedule::command('fos:billing:generate-invoices')
    ->monthlyOn(1, '02:00')
    ->withoutOverlapping();
