<?php

// namespace App\Filament\Pages;

// use Filament\Pages\Page;

// class PulsePage extends Page
// {
//     protected string $view = 'filament.pages.pulse-page';
// }

namespace App\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Dotswan\FilamentLaravelPulse\Widgets\PulseCache;
use Dotswan\FilamentLaravelPulse\Widgets\PulseExceptions;
use Dotswan\FilamentLaravelPulse\Widgets\PulseQueues;
use Dotswan\FilamentLaravelPulse\Widgets\PulseServers;
use Dotswan\FilamentLaravelPulse\Widgets\PulseSlowOutGoingRequests;
use Dotswan\FilamentLaravelPulse\Widgets\PulseSlowQueries;
use Dotswan\FilamentLaravelPulse\Widgets\PulseSlowRequests;
use Dotswan\FilamentLaravelPulse\Widgets\PulseUsage;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Page;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;

// class PulsePage extends \Filament\Pages\Dashboard
// class PulsePage extends \Filament\Pages\Dashboard
// class PulsePage extends BaseDashboard
class PulsePage extends Page
{
    use HasFiltersAction;
    use HasPageShield;

    // public function getColumns(): int|string|array
    // {
    //     return 12;
    // }

    protected int|string|array $columnSpan = 'full';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $title = 'Pulse';

    protected string $view = 'filament.pages.pulse-page';

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                // Action::make('24h')
                //     ->action(fn () => $this->redirect(route('filament.administrator.pages.pulse-page', ['period' => '24_hours']))),
                Action::make('1h')
                    ->action(fn () => $this->redirect(Reports::getUrl(['period' => '1_hour']))),
                Action::make('6h')
                    ->action(fn () => $this->redirect(Reports::getUrl(['period' => '6_hours']))),
                Action::make('24h')
                    ->action(fn () => $this->redirect(Reports::getUrl(['period' => '24_hours']))),
                Action::make('7d')
                    ->action(fn () => $this->redirect(Reports::getUrl(['period' => '7_days']))),
            ])
                ->label(__('Filter'))
                ->icon('heroicon-m-funnel')
                ->size(Size::Small)
                ->color('gray')
                ->button(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PulseUsage::class,
            PulseServers::class,
            PulseExceptions::class,
            PulseSlowRequests::class,
            PulseSlowOutGoingRequests::class,
            PulseCache::class,
            PulseQueues::class,
            PulseSlowQueries::class,
        ];
    }
}
