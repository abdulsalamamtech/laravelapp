<?php

namespace App\Providers\Filament;

use AchyutN\FilamentLogViewer\FilamentLogViewer;
use AchyutN\FilamentStorageMonitor\DTO\Disk;
use AchyutN\FilamentStorageMonitor\FilamentStorageMonitor;
use AlizHarb\ActivityLog\ActivityLogPlugin;
use App\Filament\Pages\AdminDashboard;
use App\Filament\Pages\PulsePage;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AdministratorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('administrator')
            ->path('administrator')
            ->login()
            // ->profile()
            // ->simpleProfilePage(false)
            // ->emailChangeVerification(true)
            ->colors([
                // 'primary' => Color::Amber,
                'primary' => '#0766AD',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                // Dashboard::class,
                AdminDashboard::class,
                PulsePage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                // Security - Filament Shield
                FilamentShieldPlugin::make(), // run locally - php artisan shield:generate --all --option=policies --panel=administrator
                // Activity Log
                ActivityLogPlugin::make()
                    ->label('Log')
                    ->pluralLabel('Logs')
                    // ->cluster('System') // Optional: Group inside a cluster
                    ->navigationGroup('System'),
                // Log viewer
                FilamentLogViewer::make() // view log file
                    ->authorize(fn (): bool => auth()->check() && auth()->user()->hasRole('super_admin')) // Optional: Authorization callback
                    ->navigationLabel('Logs') // Optional: Custom label for the navigation item
                    ->navigationIcon('heroicon-o-document-text') // Optional: Custom icon for the navigation item
                    ->navigationGroup('System'), // Optional: Group inside a navigation group
                // Backup
                FilamentSpatieLaravelBackupPlugin::make()
                    ->authorize(fn (): bool => auth()->check() && auth()->user()->hasRole('super_admin'))
                    ->navigationLabel('Backups')
                    ->navigationGroup('Settings'),
                // Monitor Queues/Jobs
                FilamentJobsMonitorPlugin::make()
                    ->enableNavigation(fn (): bool => auth()->check() && auth()->user()->hasRole('super_admin'))
                    ->navigationGroup('System'),
                // ->authorize(fn(): bool => auth()->check() && auth()->user()->hasRole('super_admin'))
                // ->navigationLabel('Queue Monitor')
                // ->navigationGroup('System'),
                // Storage monitoring
                FilamentStorageMonitor::make()
                    ->lazy()
                    ->compact()
                    // ->throwException(fn() => app()->isLocal())
                    ->throwException(false)
                    ->visible(fn (): bool => auth()->check() && auth()->user()->hasRole('super_admin')) // Hide entire widget
                    ->laravelDisk(name: 'local', label: 'Local Storage') // file storage disk
                    ->laravelDisk(name: 'public', label: 'Media Storage')
                    ->add(
                        Disk::make('web-root')
                            ->path('/var/www/html')
                            ->label('Web Root')
                            ->color(Color::Green)
                            ->icon(Heroicon::ComputerDesktop),
                    )
                    ->addDisk(
                        path: '/mnt/backup',
                        label: 'Backups',
                        color: Color::Blue,
                        icon: Heroicon::ArchiveBox,
                    )
                    ->addDisk(
                        path: '/mnt/data',
                        label: 'Data Partition',
                        color: Color::Blue,
                        icon: Heroicon::ArchiveBox,
                    ),
                // End of filament plugins
            ])
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
