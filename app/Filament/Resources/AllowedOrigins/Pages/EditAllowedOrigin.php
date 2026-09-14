<?php

namespace App\Filament\Resources\AllowedOrigins\Pages;

use App\Filament\Resources\AllowedOrigins\AllowedOriginResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAllowedOrigin extends EditRecord
{
    protected static string $resource = AllowedOriginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
