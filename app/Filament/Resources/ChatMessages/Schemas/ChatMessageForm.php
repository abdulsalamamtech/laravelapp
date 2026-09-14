<?php

namespace App\Filament\Resources\ChatMessages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChatMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id'),
                TextInput::make('name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                Textarea::make('message')
                    ->columnSpanFull(),
                TextInput::make('ip_address'),
            ]);
    }
}
