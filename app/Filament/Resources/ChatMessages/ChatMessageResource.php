<?php

namespace App\Filament\Resources\ChatMessages;

use App\Filament\Resources\ChatMessages\Pages\CreateChatMessage;
use App\Filament\Resources\ChatMessages\Pages\EditChatMessage;
use App\Filament\Resources\ChatMessages\Pages\ListChatMessages;
use App\Filament\Resources\ChatMessages\Pages\ViewChatMessage;
use App\Filament\Resources\ChatMessages\Schemas\ChatMessageForm;
use App\Filament\Resources\ChatMessages\Schemas\ChatMessageInfolist;
use App\Filament\Resources\ChatMessages\Tables\ChatMessagesTable;
use App\Models\ChatMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ChatMessageResource extends Resource
{
    protected static ?string $model = ChatMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleBottomCenter;

    protected static ?string $recordTitleAttribute = 'ip_address';

    // Group navigation
    protected static string|UnitEnum|null $navigationGroup = 'Tables';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return ChatMessageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChatMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChatMessagesTable::configure($table);
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
            'index' => ListChatMessages::route('/'),
            'create' => CreateChatMessage::route('/create'),
            'view' => ViewChatMessage::route('/{record}'),
            'edit' => EditChatMessage::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
