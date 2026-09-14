<?php

namespace App\Providers;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Laravel\Nightwatch\Contracts\Ingest;
use Laravel\Nightwatch\Ingest as NightwatchIngest;
use Laravel\Nightwatch\RecordsBuffer;
use Laravel\Nightwatch\SocketStreamFactory;
use Throwable;

class NightwatchServiceProvider extends ServiceProvider
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
                events: $app->make(Dispatcher::class),
            );
        });
    }
}
