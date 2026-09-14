<?php

namespace App\Filament\Resources\AllowedOrigins\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AllowedOriginForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('domain')
                    ->required()
                    ->url()
                    ->unique(ignoreRecord: true),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
