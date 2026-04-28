<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Auto Checkout Scheduler
|--------------------------------------------------------------------------
| Setiap hari pukul 00:00 WIB, sistem otomatis men-checkout semua tamu
| yang masih berstatus aktif (belum keluar).
|
| Untuk mengaktifkan di server, tambahkan cron job berikut:
| * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
*/
Schedule::command('visitors:auto-checkout')
    ->dailyAt('00:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/auto-checkout.log'));
