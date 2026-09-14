<?php

use App\Providers\NightwatchServiceProvider;
use Illuminate\Foundation\Application;
use Laravel\Nightwatch\Ingest;

it('binds the nightwatch ingest contract', function () {
    $app = Application::getInstance();
    $app->register(NightwatchServiceProvider::class);

    expect($app->bound(Laravel\Nightwatch\Contracts\Ingest::class))->toBeTrue();

    $ingest = $app->make(Laravel\Nightwatch\Contracts\Ingest::class);

    expect($ingest)->toBeInstanceOf(Ingest::class);
});
