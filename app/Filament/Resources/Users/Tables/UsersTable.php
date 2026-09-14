<?php

namespace App\Filament\Resources\Users\Tables;

use App\Jobs\ProcessReverificationEmailJob;
use App\Models\User;
use App\Services\MailService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('User Roles')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('send_unverified_mail_at')
                    ->label('Last Sent Verification Reminded')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // TrashedFilter::make(),
                TernaryFilter::make('email_verified_at')
                    ->label('Email verification')
                    ->nullable()
                    ->placeholder('All users')
                    ->trueLabel('Verified users')
                    ->falseLabel('Not verified users'),
                // Filter::make('email_verified_at')
                //     ->label('Verified Email')
                //     ->toggle(),
                Filter::make('created_at_range')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Created from')
                            ->maxDate(now()),
                        DatePicker::make('created_until')
                            ->label('Created until')
                            ->maxDate(now())->default(now()),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        )),
                // Filter to instantly view unverified users within the past two weeks
                Filter::make('unverified_recent')
                    ->label('Unverified emails (Last 2 Weeks)')
                    ->query(
                        fn (Builder $query): Builder => $query
                            ->whereNull('email_verified_at')
                            ->where('created_at', '>=', now()->subWeeks(2))
                    ),
                Filter::make('unverified_user_recent')
                    ->label('Unverified emails')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
            ])
            // ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    // restore
                    RestoreAction::make(),
                    // Row action to send email to a single user immediately
                    Action::make('send_reverify')
                        ->label('Resend Verification Mail')
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        // ->visible(fn($record) => json_decode($record->email_verified_at) === null)
                        ->visible(fn ($record): bool => ($record->email_verified_at) === null)
                        ->action(function (User $record, MailService $mailService) {

                            Log::info('Filament user - Single resend verification mail', [
                                'user_id' => $record?->id,
                                'user_email' => $record?->email,
                            ]);
                            $mailService->sendSingleReverification($record);

                            Notification::make()
                                ->title('Verification mail sent!')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    // Bulk action to send emails to all selected unverified users
                    BulkAction::make('bulk_reverify')
                        ->label('Resend verification mails')
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->action(function (Collection $records, MailService $mailService) {
                            $records->each(function ($user) {
                                if (! $user->email_verified_at) {
                                    Log::info('Filament user -Bulk resend verification mail', [
                                        'user_id' => $user?->id,
                                        'user_email' => $user?->email,
                                    ]);
                                    // $mailService->sendSingleReverification($user);
                                    // Dispatch each email safely to your background workers
                                    ProcessReverificationEmailJob::dispatch($user);
                                    Log::info('Mail Service - ProcessReverificationEmailJob dispatch', [
                                        'user_id' => $user?->id,
                                        'user_email' => $user?->email,
                                    ]);
                                }
                            });

                            Notification::make()
                                ->title('Bulk emails sent successfully!')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
