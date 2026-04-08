<?php

use App\Jobs\AutoDispatchDigests;
use App\Jobs\FetchDiscordSources;
use App\Jobs\FetchRssSources;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new FetchRssSources)->hourly();
Schedule::job(new FetchDiscordSources)->hourly();
Schedule::job(new AutoDispatchDigests)->hourly();
