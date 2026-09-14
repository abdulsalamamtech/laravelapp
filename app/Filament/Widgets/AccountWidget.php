<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\AccountWidget as BaseAccountWidget;
use Filament\Widgets\Widget;

class AccountWidget extends BaseAccountWidget
{
    use HasWidgetShield;

    // protected string $view = 'filament.widgets.account-widget';
    // Make the widget span the full width of the grid
    protected int|string|array $columnSpan = 'full';
}
