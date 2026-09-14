<?php

namespace App\Filament\Resources\AllowedOrigins\Pages;

use App\Filament\Resources\AllowedOrigins\AllowedOriginResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAllowedOrigins extends ListRecords
{
    protected static string $resource = AllowedOriginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
