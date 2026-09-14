<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(false),
                TextInput::make('collection_name')
                    ->required(false),
                TextInput::make('mime_type')
                    ->required(false),
                TextInput::make('size')
                    ->suffix('KB')
                    ->required(false),
                // Custom view to safely preview images or embed PDFs
                // ViewField::make('file_preview')
                //     ->label('File Preview')
                //     ->view('filament.forms.components.media-preview') // Custom Blade view below
                //     ->columnSpanFull(),

                TextInput::make('file_name')
                    ->disabled(), // Safer to keep disabled so disk paths don't break

                TextInput::make('disk')
                    ->disabled(),
                TextInput::make('conversions_disk')
                    ->disabled(),

                TextInput::make('preview_url')
                    ->label('Preview Link')
                    ->dehydrated(false) // Computed display-only field, not stored in the DB
                    ->suffixAction(
                        Action::make('openUrl')
                            ->icon('heroicon-m-arrow-top-right-on-square')
                            ->tooltip('Open preview link')
                            // ->url(fn ($state) => $state) // Dynamically grabs the field value
                            ->url(fn ($record) => $record->getUrl())
                            ->openUrlInNewTab()
                            ->hidden(fn ($state): bool => empty($state)) // Hides the button if field is empty
                    ),

                Section::make('Data')
                    ->schema([
                        KeyValue::make('manipulations')
                            ->label('Metadata Log')
                            ->disabled() // Makes the entire component read-only
                            ->dehydrated(false), // Prevents transmitting data back to server
                        KeyValue::make('custom_properties')
                            // ->label('Metadata Log')
                            ->disabled()
                            ->dehydrated(false),
                        KeyValue::make('generated_conversions')
                            // ->label('Metadata Log')
                            ->disabled()
                            ->dehydrated(false),
                        KeyValue::make('responsive_images')
                            // ->label('Metadata Log')
                            ->disabled()
                            ->dehydrated(false),

                        // OTHERS
                        Textarea::make('custom_properties')
                            ->formatStateUsing(fn ($state): string|false => json_encode($state, JSON_PRETTY_PRINT))
                            ->disabled()
                            ->rows(10),
                    ])->columnSpanFull(),
            ]);
    }
}
