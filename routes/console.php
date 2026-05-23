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

Schedule::command('reports:weekly-summary')
    ->weeklyOn(1, '07:00')
    ->withoutOverlapping();

Schedule::command('school:generate-monthly-tuition')
    ->monthlyOn(1, '03:00')
    ->withoutOverlapping();

Schedule::command('school:apply-late-fees')
    ->dailyAt('06:00')
    ->withoutOverlapping();

Schedule::command('legal:check-expiring-contracts')
    ->dailyAt('07:00')
    ->withoutOverlapping();

Schedule::command('helpdesk:check-sla')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

Schedule::command('asset:check-maintenance-due')
    ->dailyAt('06:30')
    ->withoutOverlapping();

Schedule::command('asset:check-insurance-expiry')
    ->dailyAt('06:45')
    ->withoutOverlapping();

Schedule::command('dms:archive-expired')
    ->dailyAt('02:30')
    ->withoutOverlapping();

Schedule::command('itops:check-expiry')
    ->dailyAt('07:30')
    ->withoutOverlapping();

Schedule::command('cafeteria:settle-tenants')
    ->weeklyOn(1, '04:00')
    ->withoutOverlapping();

Schedule::command('safety:daily-rounds')
    ->dailyAt('05:00')
    ->withoutOverlapping();

Schedule::command('school:recompute-risk-scores')
    ->dailyAt('04:30')
    ->withoutOverlapping();

Schedule::command('counseling:scan-risk')
    ->dailyAt('05:30')
    ->withoutOverlapping();

Schedule::command('comms:send-newsletter')
    ->weeklyOn(1, '08:00')
    ->withoutOverlapping();

Schedule::command('alumni:tracer-study-blast')
    ->yearlyOn(7, 1, '09:00')
    ->withoutOverlapping();

Schedule::command('enrollment:lead-followup-due')
    ->cron('0 */4 * * *')
    ->withoutOverlapping();

Schedule::command('donation:send-campaign-update')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('donation:charge-recurring')
    ->dailyAt('02:15')
    ->withoutOverlapping();

Schedule::command('training:settle-affiliate')
    ->weeklyOn(1, '05:00')
    ->withoutOverlapping();

Schedule::command('printing:calculate-royalty-monthly')
    ->monthlyOn(1, '03:30')
    ->withoutOverlapping();

Schedule::command('property:generate-monthly-lease-invoice')
    ->monthlyOn(1, '04:00')
    ->withoutOverlapping();

Schedule::command('workflow:escalate-overdue')
    ->hourly()
    ->withoutOverlapping();
