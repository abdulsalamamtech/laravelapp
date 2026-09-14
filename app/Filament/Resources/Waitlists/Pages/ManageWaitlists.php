<?php

namespace App\Filament\Resources\Waitlists\Pages;

use App\Filament\Resources\Waitlists\WaitlistResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWaitlists extends ManageRecords
{
    protected static string $resource = WaitlistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
