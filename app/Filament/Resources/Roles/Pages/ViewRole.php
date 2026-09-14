<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected static $protectedRoles = ['super-admin', 'super_admin', 'admin', 'company']; // self::$protectedRoles

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->hidden(fn ($record): bool => in_array($record->name, self::$protectedRoles)),
            DeleteAction::make()->hidden(fn ($record): bool => in_array($record->name, self::$protectedRoles)),
        ];
    }
}
