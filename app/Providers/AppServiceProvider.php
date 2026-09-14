<?php

namespace App\Providers;

use App\Models\System\PersonalAccessToken;
use App\Models\User;
use BezhanSalleh\FilamentShield\Commands;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Filament\Pages\BasePage as Page;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Laravel\Nightwatch\Contracts\Ingest;
use Laravel\Nightwatch\Ingest as NightwatchIngest;
use Laravel\Nightwatch\RecordsBuffer;
use Laravel\Nightwatch\SocketStreamFactory;
use Laravel\Sanctum\Sanctum;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(function ($app): Ingest {
            $nightwatchConfig = [];

            try {
                if ($app->bound(ConfigRepository::class)) {
                    $nightwatchConfig = $app->make(ConfigRepository::class)->get('nightwatch', []);
                }
            } catch (Throwable) {
                $nightwatchConfig = [];
            }

            $ingestConfig = $nightwatchConfig['ingest'] ?? [];
            $tokenHash = substr(hash('xxh128', (string) ($nightwatchConfig['token'] ?? '')), 0, 7);

            return new NightwatchIngest(
                transmitTo: (string) ($ingestConfig['uri'] ?? '127.0.0.1:2407'),
                connectionTimeout: (float) ($ingestConfig['connection_timeout'] ?? 0.5),
                timeout: (float) ($ingestConfig['timeout'] ?? 0.5),
                streamFactory: new SocketStreamFactory,
                buffer: new RecordsBuffer(length: (int) ($ingestConfig['event_buffer'] ?? 500)),
                tokenHash: $tokenHash,
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Force register the missing infolists namespace for the static view compiler
        Blade::componentNamespace('Filament\\Infolists\\Components', 'filament-infolists');
        // Fixes the static CLI view compiler crash by creating a fallback mapping
        // Bypasses the view compiler exception block in local & CI pipelines
        if (App::runningInConsole()) {
            Blade::component('filament::grid', Component::class);
            Blade::component('filament-infolists::entries.placeholder', Component::class);
        }

        // Use the Gate to implicitly grant all permissions to the super_admin
        Gate::before(function (User $user, $ability): ?true {
            // cache the log to prevent log flooding in case of multiple authorization checks
            static $cachedLogs = [];
            $logKey = $user->id.'_'.$ability;
            if (! isset($cachedLogs[$logKey])) {
                $cachedLogs[$logKey] = true;
                Log::info('Gate before access: (user super admin)', [$user]);
            }

            // If the user has the super_admin role, return true to grant access immediately
            return $user->hasRole(config('filament-shield.super_admin.name')) ? true : null;
        });

        // View pulse
        Gate::define('viewPulse', fn (User $user): ?true => $user->hasRole('super_admin') ? true : null);

        // individually prohibit commands
        Commands\GenerateCommand::prohibit($this->app->isProduction());
        Commands\InstallCommand::prohibit($this->app->isProduction());
        Commands\PublishCommand::prohibit($this->app->isProduction());
        Commands\SetupCommand::prohibit($this->app->isProduction());
        Commands\SeederCommand::prohibit($this->app->isProduction());
        Commands\SuperAdminCommand::prohibit($this->app->isProduction());
        // or prohibit the above commands all at once
        FilamentShield::prohibitDestructiveCommands($this->app->isProduction());

        // View scrambled API docs only for authenticated users
        Gate::define('viewApiDocs', function (?User $user) {
            // Option A: Allow all logged-in users
            return auth()->check();

            // Option B: Allow only specific email (Safer)
            // return in_array($user->email, ['admin@example.com']);
        });
        // Scrample API Docs Token
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        Scramble::registerApi('v1', [
            'api_path' => 'api/v1',
        ]);
        Scramble::registerApi('v2', ['info' => ['version' => '2.0']])

            ->routes(fn (Route $route) => Str::startsWith($route->uri, 'api/'))
            ->afterOpenApiGenerated(function (OpenApi $openApi) {
                // Some operations on the resulting documentation.
            });

        // Changing the token from id to uuid
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // Preserve Existing Data: If you want to keep your existing roles and permissions intact, then follow these steps:
        FilamentShield::buildPermissionKeyUsing(
            fn (string $entity, string $affix, string $subject, string $case, string $separator) => match (true) {
                // if `configurePermissionIdentifierUsing()` was used previously, then this needs to be adjusted accordingly
                is_subclass_of($entity, Resource::class) => Str::of($affix)
                    ->snake()
                    ->append('_')
                    ->append(
                        Str::of($entity)
                            ->afterLast('\\')
                            ->beforeLast('Resource')
                            ->replace('\\', '')
                            ->snake()
                            ->replace('_', '::')
                    )
                    ->toString(),
                is_subclass_of($entity, Page::class) => Str::of('page_')
                    ->append(class_basename($entity))
                    ->toString(),
                is_subclass_of($entity, Widget::class) => Str::of('widget_')
                    ->append(class_basename($entity))
                    ->toString()
            }
        );

        // PLAN - PRIVILEGE / PERMISSION
        // Docs: https://share.google/aimode/dQKZLCcgFj3riXITn
        // To make your privileges - permissions hook natively into Laravel's @can directives, request helpers, and policies
        // Don't run this during migrations to avoid errors
        // {{-- Check for a specific feature permission --}}
        // @can('export_reports')
        //     <button>Export CSV</button>
        // @endcan

        // {{-- Check for an explicit plan level --}}
        // @if(auth()->user()->hasPlan('pro_plus'))
        //     <p>Welcome VIP Member!</p>
        // @endif

        // NOTE: The Privilege/Company domain (App\Models\Privilege, privileges table) is
        // not currently present in this codebase. The dynamic privilege gates below are
        // deactivated until the module is restored.

        // Example: Allow 10 document requests per minute
        RateLimiter::for('llm-processing', fn (object $job) => Limit::perMinute(10)->by('llm-api-provider'));
    }
}
