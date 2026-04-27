<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

use Illuminate\Support\Facades\Schedule;
use App\Models\PerformanceMetric;

// Prune old performance metrics daily
Schedule::call(function () {
    PerformanceMetric::where('created_at', '<', now()->subDays(30))->delete();
})->daily();

// Alert administrators if performance drops
Schedule::command('performance:alert')->everyFiveMinutes();
