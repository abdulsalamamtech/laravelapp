<?php

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;

function qualityProcessCommand(PendingProcess $process): string
{
    return implode(' ', (array) $process->command);
}

it('runs the full quality suite in read-only mode by default', function () {
    Process::fake();

    $this->artisan('app:check')
        ->expectsOutputToContain('[PASS] lint')
        ->expectsOutputToContain('[PASS] analyse')
        ->expectsOutputToContain('[PASS] rector')
        ->expectsOutputToContain('[PASS] tests')
        ->assertExitCode(0);

    Process::assertRanInOrder([
        fn (PendingProcess $process): bool => str_contains(qualityProcessCommand($process), 'pint') && str_contains(qualityProcessCommand($process), '--test'),
        fn (PendingProcess $process): bool => str_contains(qualityProcessCommand($process), 'phpstan') && str_contains(qualityProcessCommand($process), 'analyse'),
        fn (PendingProcess $process): bool => str_contains(qualityProcessCommand($process), 'rector') && str_contains(qualityProcessCommand($process), '--dry-run'),
        fn (PendingProcess $process): bool => str_contains(qualityProcessCommand($process), 'pest'),
    ]);
});

it('applies fixes when the --fix option is used', function () {
    Process::fake();

    $this->artisan('app:check --fix')
        ->assertExitCode(0);

    Process::assertRan(fn (PendingProcess $process): bool => str_contains(qualityProcessCommand($process), 'pint') && ! str_contains(qualityProcessCommand($process), '--test'));
    Process::assertRan(fn (PendingProcess $process): bool => str_contains(qualityProcessCommand($process), 'rector') && ! str_contains(qualityProcessCommand($process), '--dry-run'));
});

it('skips the requested stages', function () {
    Process::fake();

    $this->artisan('app:check --skip=analyse --skip=rector')
        ->expectsOutputToContain('Skipped stage: analyse')
        ->expectsOutputToContain('Skipped stage: rector')
        ->assertExitCode(0);

    Process::assertNotRan(fn (PendingProcess $process): bool => str_contains(qualityProcessCommand($process), 'phpstan'));
    Process::assertNotRan(fn (PendingProcess $process): bool => str_contains(qualityProcessCommand($process), 'rector'));
    Process::assertRan(fn (PendingProcess $process): bool => str_contains(qualityProcessCommand($process), 'pest'));
});

it('returns a failure exit code when a stage fails', function () {
    Process::fake([
        '*pint*' => Process::result(exitCode: 1),
        '*' => Process::result(exitCode: 0),
    ]);

    $this->artisan('app:check')
        ->expectsOutputToContain('[FAIL] lint')
        ->assertExitCode(1);
});
