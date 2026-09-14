<?php

use App\Models\OtpToken;
use App\Services\EmailChangeService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('queue:work --stop-when-empty --timeout=300 --tries=3 --backoff=60')
// ->sendOutputTo($filePath)
// ->emailOutputTo('taylor@example.com')
// ->everyMinute()
// ->everyTwoMinutes()
// ->everyFiveMinutes()
// ->everyTenMinutes()
// Run the queue worker every minute, stopping cleanly when all jobs are done
// Schedule::command('queue:work', [
//     // '--stop-when-empty' => true,
//     '--stop-when-empty',
//     '--timeout' => 300, // the best queue timeout is typically 300 to 600 seconds (5 to 10 minutes) - DB_QUEUE_RETRY_AFTER=360 // Must be higher than your job $timeout 300
//     '--tries' => 3,
//     '--backoff' => 60 // Waits 60 seconds before retrying a failed attempt - if an LLM API rate-limits you, the job waits a few minutes before trying again
// ])

Schedule::command('queue:work --stop-when-empty --timeout=300 --tries=3 --backoff=60')
    ->everyMinute()
    ->emailOutputOnFailure(config('mail.data.dev'))
    ->runInBackground()
    ->withoutOverlapping()
    ->onSuccess(function () {
        // The task succeeded...
        Log::info('Scheduled task succeeded: queue:work --stop-when-empty');
    })
    ->onFailure(function () {
        // The task failed...
        Log::error('Scheduled task failed: queue:work --stop-when-empty');
    })
    ->before(function () {
        // The task is about to execute...
        Log::info('Starting scheduled task: queue:work --stop-when-empty');
    })
    ->after(function () {
        // The task has executed...
        Log::info('Completed scheduled task: queue:work --stop-when-empty');
    });

// php artisan schedule:work
// php artisan schedule:list
// php artisan schedule:pause
// php artisan queue:continue
// * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
// /usr/local/bin/php /home/u123456789/domains/://yourdomain.com schedule:run >> /dev/null 2>&1
// /usr/local/bin/php /home/u123456789/domains/://yourdomain.com queue:work --stop-when-empty
// public_html/staging && php artisan schedule:run >> /dev/null 2>&1

// /usr/local/bin/php /home/u2006738909/
// domains/veriscore.app/public_html/staging && php artisan schedule:run >> /dev/null 2>&1

// php artisan nightwatch:agent
if (config('nightwatch.enabled', false)) {
    Schedule::command('nightwatch:agent')
        ->everyMinute()
        ->emailOutputOnFailure(config('mail.data.dev'))
        ->runInBackground()
        ->withoutOverlapping()
        ->onSuccess(function () {
            // The task succeeded...
            Log::info('Scheduled task succeeded: nightwatch:agent');
        })
        ->onFailure(function () {
            // The task failed...
            Log::error('Scheduled task failed: nightwatch:agent');
        })
        ->before(function () {
            // The task is about to execute...
            Log::info('Starting scheduled task: nightwatch:agent');
        })
        ->after(function () {
            // The task has executed...
            Log::info('Completed scheduled task: nightwatch:agent');
        });
}

// Run every hour to check for chunks of unverified users
Schedule::command('app:send-reverification-emails')->hourly();

// Remove expired OTP tokens daily
Schedule::call(function () {
    $deleted = OtpToken::where('expires', '<', now())->delete();

    Log::info('Expired OTP tokens cleaned up: '.$deleted);
})->daily()->name('otp-token-cleanup');

// Apply due email changes and expire stale ones before the window closes
Schedule::call(function () {
    $service = app(EmailChangeService::class);
    $applied = $service->applyDue();
    $expired = $service->expireStale();

    Log::info('Email change housekeeping - applied: '.$applied.', expired: '.$expired);
})->hourly()->name('email-change-housekeeping');
