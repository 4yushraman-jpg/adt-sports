<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Fold buffered page views into articles.views off the request path.
Schedule::command('app:flush-article-views')->everyFiveMinutes()->withoutOverlapping();
