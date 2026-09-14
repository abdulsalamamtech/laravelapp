<?php

namespace App\Filament\Resources\AllowedOrigins;

use App\Filament\Resources\AllowedOrigins\Pages\CreateAllowedOrigin;
use App\Filament\Resources\AllowedOrigins\Pages\EditAllowedOrigin;
use App\Filament\Resources\AllowedOrigins\Pages\ListAllowedOrigins;
use App\Filament\Resources\AllowedOrigins\Schemas\AllowedOriginForm;
use App\Filament\Resources\AllowedOrigins\Tables\AllowedOriginsTable;
use App\Models\Custom\AllowedOrigin;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AllowedOriginResource extends Resource
{
    protected static ?string $model = AllowedOrigin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AllowedOriginForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AllowedOriginsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAllowedOrigins::route('/'),
            'create' => CreateAllowedOrigin::route('/create'),
            'edit' => EditAllowedOrigin::route('/{record}/edit'),
        ];
    }
}
