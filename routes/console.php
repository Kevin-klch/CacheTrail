<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Membership badges depend only on time passing, so all users are checked once a day.
Schedule::command('badges:award')->dailyAt('03:00');

// Deletes old login records (see LoginRecord::prunable()).
Schedule::command('model:prune')->dailyAt('03:15');
