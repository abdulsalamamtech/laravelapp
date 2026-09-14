<?php

namespace App\Filament\Pages;

use AchyutN\FilamentStorageMonitor\Widgets\StorageMonitorWidget;
use AlizHarb\ActivityLog\Widgets\ActivityChartWidget;
use AlizHarb\ActivityLog\Widgets\ActivityHeatmapWidget;
use AlizHarb\ActivityLog\Widgets\ActivityStatsWidget;
use AlizHarb\ActivityLog\Widgets\LatestActivityWidget;
use App\Filament\Widgets\AccountWidget;
use App\Filament\Widgets\StatsOverview;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;

class AdminDashboard extends BaseDashboard
{
    use HasPageShield;

    // protected string $view = 'filament.pages.admin-dashboard';

    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            StatsOverview::class,
            ActivityStatsWidget::class,
            StorageMonitorWidget::class,
            ActivityChartWidget::class,
            ActivityHeatmapWidget::class,
            LatestActivityWidget::class,

        ];
    }
}
