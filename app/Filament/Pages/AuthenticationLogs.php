<?php

namespace App\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;
use UnitEnum;

class AuthenticationLogs extends Page implements HasTable
{
    use HasPageShield;
    use InteractsWithTable;

    protected string $view = 'filament.pages.authentication-logs';

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    // navigation group for the page
    protected static UnitEnum|string|null $navigationGroup = 'System';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AuthenticationLog::query()
            )
            ->columns([
                TextColumn::make('authenticatable_type')
                    ->label('User')
                    ->formatStateUsing(function ($state, Model $record): string {
                        // return $state . ' (' . $record->authenticatable_id . ')';
                        // Get the user model instance
                        $userModel = app($record->authenticatable_type)::find($record->authenticatable_id);

                        // Return the user type with the user's name if available
                        // return $state . ' (' . ($userModel ? $userModel->name : 'Unknown User') . ')');
                        return $userModel ? ($userModel->name.' ('.$userModel->email.')') : 'Unknown User';
                    }),

                TextColumn::make('ip_address')->label('IP Address'),
                TextColumn::make('device_name')
                    ->label('Browser/Device')
                    ->searchable()
                    ->default('Unknown Device'),
                TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->searchable()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                ColumnGroup::make('location')
                    ->label('Location')
                    ->columns([
                        TextColumn::make('location.city')
                            ->label('City')
                            ->searchable(),
                        TextColumn::make('location.state')
                            ->label('State')
                            ->searchable(),
                        TextColumn::make('location.state_name')
                            ->label('State Name')
                            ->searchable(),
                        TextColumn::make('location.postal_code')
                            ->label('Postal Code')
                            ->searchable(),
                        TextColumn::make('location.country')
                            ->label('Country')
                            ->searchable(),
                        TextColumn::make('location.currency')
                            ->label('Currency')
                            ->searchable(),
                        // image for country flag if available
                        // ImageColumn::make('location.flag')
                        //     ->label('Flag')
                        //     ->height(20)
                        //     ->width(30)
                        //     ->rounded()
                        //     ->placeholderImage('https://via.placeholder.com/30x20?text=No+Flag'),
                        TextColumn::make('location.iso_code')
                            ->label('Country Code')
                            ->searchable(),
                        TextColumn::make('location.lat')
                            ->label('Latitude')
                            ->searchable(),
                        TextColumn::make('location.lon')
                            ->label('Longitude')
                            ->searchable(),
                        TextColumn::make('location.timezone')
                            ->label('Timezone')
                            ->searchable(),
                        TextColumn::make('location.is_eu')
                            ->label('EU')
                            ->sortable(),
                        TextColumn::make('location.is_proxy')
                            ->label('Proxy')
                            ->sortable(),

                    ]),
                IconColumn::make('login_successful')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                IconColumn::make('is_trusted')
                    ->label('Trusted')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_suspicious')
                    ->label('Suspicious')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('warning')
                    ->sortable(),
                TextColumn::make('login_at')
                    ->label('Login At')
                    ->dateTime()
                    ->sortable()
                    ->default(''),
                TextColumn::make('logout_at')
                    ->label('Logout At')
                    ->dateTime()
                    ->sortable()
                    ->default(''),
                TextColumn::make('last_activity_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable()
                    ->default(''),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('login_successful')
                    ->label('Login Status')
                    ->placeholder('All logins')
                    ->trueLabel('Successful only')
                    ->falseLabel('Failed only'),
                Tables\Filters\TernaryFilter::make('is_trusted')
                    ->label('Trusted Device')
                    ->placeholder('All devices')
                    ->trueLabel('Trusted only')
                    ->falseLabel('Untrusted only'),
                Tables\Filters\TernaryFilter::make('is_suspicious')
                    ->label('Suspicious Activity')
                    ->placeholder('All activities')
                    ->trueLabel('Suspicious only')
                    ->falseLabel('Normal only'),
                Tables\Filters\Filter::make('active_sessions')
                    ->label('Active Sessions')
                    ->query(
                        fn (Builder $query): Builder => $query
                            ->where('login_successful', true)
                            ->whereNull('logout_at')
                    ),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        // for the user
                        TextColumn::make('authenticatable_type')
                            ->label('User Type')
                            ->formatStateUsing(fn ($state, Model $record): string => $state.' ('.$record->authenticatable_id.')'),

                        TextEntry::make('ip_address')->label('IP Address')->disabled(),
                        TextEntry::make('device_name')->label('Browser/Device')->disabled(),
                        TextEntry::make('user_agent')->label('User Agent')->disabled()->columnSpanFull(),
                        TextEntry::make('location.city')->label('City')->disabled(),
                        TextEntry::make('location.state')->label('State')->disabled(),
                        TextEntry::make('location.state_name')->label('State Name')->disabled(),
                        TextEntry::make('location.postal_code')->label('Postal Code')->disabled(),
                        TextEntry::make('location.country')->label('Country')->disabled(),
                        TextEntry::make('location.iso_code')->label('Country Code')->disabled(),
                        TextEntry::make('location.lat')->label('Latitude')->disabled(),
                        TextEntry::make('location.lon')->label('Longitude')->disabled(),
                        TextEntry::make('location.timezone')->label('Timezone')->disabled(),
                        TextEntry::make('login_successful')
                            ->label('Login Successful')
                            ->disabled(),
                        TextEntry::make('is_trusted')
                            ->label('Trusted Device')
                            ->disabled(),
                        IconEntry::make('is_suspicious')
                            ->label('Suspicious Activity')
                            ->disabled(),
                        TextEntry::make('login_at')
                            ->label('Login At')
                            ->dateTime()
                            ->disabled(),
                        TextEntry::make('logout_at')
                            ->label('Logout At')
                            ->dateTime()
                            ->disabled(),
                        TextEntry::make('last_activity_at')
                            ->label('Last Activity At')
                            ->dateTime()
                            ->disabled(),
                    ])
                    ->slideOver()
                    ->modalHeading('Authentication Log Details'),
            ])
            ->defaultSort('login_at', 'desc')
            ->poll('30s'); // Optional: auto-refresh every 30 seconds
    }
}
