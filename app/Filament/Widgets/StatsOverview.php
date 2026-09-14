<?php

namespace App\Filament\Widgets;

use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends StatsOverviewWidget
{
    use HasWidgetShield;

    // protected ?string $pollingInterval = '60s';
    protected ?string $pollingInterval = null;

    protected function getHeading(): ?string
    {
        return 'Analytics';
    }

    protected function getDescription(): ?string
    {
        return 'An overview of some analytics.';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('All Users', $this->data('all_users_count'))
                ->description('Total registered users'),
            Stat::make('Verified Users', $this->data('verified_users'))
                ->description('Total users with verified email'),
            Stat::make('New Users (Today)', $this->data('today_users'))
                ->description('Total users that register today'),
            // Stat::make('Unique Users', $this->data('unique_users_count'))
            //     ->description('Total unique users'),
            // Stat::make('Unique Verified Users', $this->data('unique_verified_users_count'))
            //     ->description('Total unique users that have activated their account'),
            // Stat::make('Pending Users', $this->data('pending_count'))
            //     ->description('Total users without verified email'),
        ];
    }

    private function data($key = null)
    {
        // Fetch all metrics in a single database query using Laravel's withCount
        // Docs: https://share.google/aimode/L3E7b3CvagwPesqZZ
        $stats = User::query()
            ->selectRaw("
                COUNT(CASE WHEN email IS NOT NULL THEN 1 END) as all_users_count,
                COUNT(CASE WHEN email NOT LIKE '%@veriscore.app' AND email NOT LIKE '%@gluedinsights.com' THEN 1 END) as unique_users_count,
                COUNT(CASE WHEN email NOT LIKE '%@veriscore.app' AND email NOT LIKE '%@gluedinsights.com' THEN 1 END AND CASE WHEN email_verified_at IS NOT NULL THEN 1 END) as unique_verified_users_count,
                COUNT(CASE WHEN email_verified_at IS NOT NULL THEN 1 END) as verified_count,
                COUNT(CASE WHEN email_verified_at IS NULL THEN 1 END) as pending_count,
                COUNT(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 END) as today_count
            ")
            ->first();

        $value = match (true) {
            $key == 'all_users_count' => $stats?->all_users_count ?? 0,
            $key == 'verified_users' => $stats?->verified_count ?? 0,
            $key == 'unique_users_count' => $stats?->unique_users_count ?? 0,
            $key == 'unique_verified_users_count' => $stats?->unique_verified_users_count ?? 0,
            $key == 'today_users' => $stats?->today_count ?? 0,
            $key == 'pending_users' => $stats?->pending_count ?? 0,
            default => 0,
        };

        return $value > 999 ? Number::abbreviate($value, 2) : Number::abbreviate($value, 0);
    }
}
