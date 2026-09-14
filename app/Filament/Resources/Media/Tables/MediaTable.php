<?php

namespace App\Filament\Resources\Media\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                // TextColumn::make('name')
                //     ->searchable(),
                // TextColumn::make('collection_name')
                //     ->searchable(),
                // TextColumn::make('mine_type')
                //     ->sortable(),
                // TextColumn::make('file_name')
                //     ->searchable(),

                // Displays thumbnails from the default collection
                // SpatieMediaLibraryImageColumn::make('collection_name')
                //     ->label('Document')
                //     // ->collection('gallery') // Explicitly state your collection name
                //     // ->conversion('thumb')  // Optional: load a specific conversion size
                //     ->circular()           // Optional: makes the thumbnails round
                //     ->stacked(),           // Optional: stacks multiple images neatly

                // 1. Renders visual thumbnail previews for image records
                // SpatieMediaLibraryImageColumn::make('media')
                //     ->collection('attachments')
                //     ->conversion('thumb')
                //     ->label('Image Preview'),

                // // 2. Renders a clickable link for documents like PDFs/Docs
                // TextColumn::make('name')
                //     ->label('Document Link')
                //     ->description(fn($record) => $record->getMedia('attachments')->first()?->file_name)
                //     ->url(fn($record) => $record->getMedia('attachments')->first()?->getUrl())
                //     ->openUrlInNewTab()
                //     ->color('primary'),

                // 1. Renders visual thumbnail previews for image records
                // SpatieMediaLibraryImageColumn::make('media')
                //     ->collection('attachments')
                //     ->conversion('thumb')
                //     ->label('Image Preview'),

                // // 2. Renders a clickable link for documents like PDFs/Docs
                // TextColumn::make('name')
                //     ->label('Document Link')
                //     ->description(fn($record) => $record->getMedia('attachments')->first()?->file_name)
                //     ->url(fn($record) => $record->getMedia('attachments')->first()?->getUrl())
                //     ->openUrlInNewTab()
                //     ->color('primary'),

                // 1. Conditional Thumbnail View
                ImageColumn::make('file_preview')
                    ->label('Preview')
                    ->state(
                        // Only return a URL if the record is actually an image
                        fn ($record) => str_starts_with($record->mime_type, 'image/') ? $record?->getUrl() : null)
                    ->size(40)
                    // Fallback icon for PDFs, Word files, etc.
                    ->defaultImageUrl('https://static.vecteezy.com/system/resources/previews/057/655/444/large_2x/stunning-creative-legal-document-icon-transparent-background-original-png.png'),

                // ImageColumn::make('document_preview')
                //     ->state(function (Model $record): string {
                //         try {
                //             // Force return fallback if no media exists at all
                //             if (! $record->hasMedia('documents')) {
                //                 return asset('images/default-preview.png');
                //             }
                //             return $record->getFirstMediaUrl('documents');
                //         } catch (\Throwable $e) {
                //             // Catch storage NotFound exceptions silently
                //             return asset('images/default-preview.png');
                //         }
                //     }),

                // 2. Direct File Name and Link
                TextColumn::make('file_name')
                    ->label('File Name')
                    ->searchable()
                    ->sortable()
                    // ->url(fn($record) => $record?->getUrl() ?? '')
                    // rescue(callable $callback, mixed $rescue = null, bool $report = true)
                    ->url(fn ($record) => rescue(fn () => $record?->getUrl() ?? '', '', false)) // using try-catch
                    ->openUrlInNewTab()
                    ->color('primary'),

                TextColumn::make('mime_type')
                    ->label('Type')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state): string => number_format(((int) $state) / 1024, 2).' KB')
                    ->numeric()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Add this line to force descending order by default
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
