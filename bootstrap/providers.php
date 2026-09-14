<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdministratorPanelProvider;
use App\Providers\NightwatchServiceProvider;

return [
    NightwatchServiceProvider::class,
    AppServiceProvider::class,
    AdministratorPanelProvider::class,
];
