<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

#[Description('Run the project quality suite: Pint, PHPStan, Rector, and Pest tests.')]
#[Signature('app:check {--fix : Apply Pint fixes and Rector changes instead of dry-run} {--skip=* : Skip stages: lint, analyse, rector, tests}')]
class QualityCheck extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $skip = array_map(strtolower(...), $this->option('skip') ?? []);
        $fix = (bool) $this->option('fix');

        $stages = [
            'lint' => fn (): int => $this->runStage('Lint (Pint)', $this->pintCommand($fix)),
            'analyse' => fn (): int => $this->runStage('Analyse (PHPStan)', $this->phpstanCommand()),
            'rector' => fn (): int => $this->runStage('Rector', $this->rectorCommand($fix)),
            'tests' => fn (): int => $this->runStage('Tests (Pest)', $this->pestCommand()),
        ];

        $exitCodes = [];

        foreach ($stages as $name => $stage) {
            if (in_array($name, $skip, true)) {
                $this->warn("Skipped stage: {$name}");

                continue;
            }

            $exitCodes[$name] = $stage();
        }

        $this->printSummary($exitCodes);

        return in_array(self::FAILURE, $exitCodes, true) ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Run a single quality stage and stream its output live.
     */
    private function runStage(string $label, array $command): int
    {
        $this->newLine();
        $this->info("Running {$label}");

        $result = Process::path(base_path())
            ->timeout(300)
            ->run($command, function (string $type, string $output): void {
                $this->line(rtrim($output));
            });

        if ($result->successful()) {
            $this->info("{$label}: PASSED");
        } else {
            $this->error("{$label}: FAILED (exit code {$result->exitCode()})");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Pint command. Dry-run (read-only) unless --fix is passed.
     */
    private function pintCommand(bool $fix): array
    {
        $command = [base_path('vendor/bin/pint')];

        if (! $fix) {
            $command[] = '--test';
        }

        return $command;
    }

    /**
     * PHPStan static analysis.
     */
    private function phpstanCommand(): array
    {
        return [base_path('vendor/bin/phpstan'), 'analyse', '--no-progress', '--no-interaction'];
    }

    /**
     * Rector command. Dry-run (read-only) unless --fix is passed.
     */
    private function rectorCommand(bool $fix): array
    {
        $command = [base_path('vendor/bin/rector'), 'process', '--no-progress-bar'];

        if (! $fix) {
            $command[] = '--dry-run';
        }

        return $command;
    }

    /**
     * Pest test suite.
     */
    private function pestCommand(): array
    {
        return [base_path('vendor/bin/pest'), '--colors=always'];
    }

    /**
     * Print a final per-stage summary.
     */
    private function printSummary(array $exitCodes): void
    {
        $this->newLine();
        $this->info('Quality check summary:');

        foreach ($exitCodes as $stage => $code) {
            $status = $code === self::SUCCESS ? '<info>[PASS]</info>' : '<error>[FAIL]</error>';

            $this->line("  {$status} {$stage}");
        }
    }
}
