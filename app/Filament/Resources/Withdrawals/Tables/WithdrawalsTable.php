<?php

namespace App\Filament\Resources\Withdrawals\Tables;

use App\Enums\WithdrawalStatus;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use Filament\Notifications\Notification;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;

class WithdrawalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->with(['user.country']))
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('filament.fields.user'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('amount')
                    ->label(__('filament.fields.amount'))
                    ->formatStateUsing(function ($state, $record) {
                        $unit = $record->user->country->currency_unit ?? 'EGP';
                        $systemBase = config('settings.currency.system_base', 'USD');
                        $finalPrice = app(\App\Services\CurrencyService::class)->convert((float) $state, $systemBase, $unit);
                        return number_format($finalPrice, 2) . ' ' . $unit;
                    })
                    ->sortable(),


                TextColumn::make('net_amount')
                    ->label(__('filament.fields.total_amount'))
                    ->formatStateUsing(function ($state, $record) {
                        $unit = $record->user->country->currency_unit ?? 'EGP';
                        $systemBase = config('settings.currency.system_base', 'USD');
                        $finalPrice = app(\App\Services\CurrencyService::class)->convert((float) $state, $systemBase, $unit);
                        return number_format($finalPrice, 2) . ' ' . $unit;
                    })
                    ->sortable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->formatStateUsing(function ($state) {
                                $currencyService = app(\App\Services\CurrencyService::class);
                                $systemBase = config('settings.currency.system_base', 'USD');
                                $finalPriceInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                                return number_format($finalPriceInEGP, 2) . ' EGP';
                            })
                            ->label(__('filament.withdrawal.total_amount')),
                    ]),


                \Filament\Tables\Columns\BadgeColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->color(fn(WithdrawalStatus $state): string => $state->getColor())
                    ->formatStateUsing(fn(WithdrawalStatus $state): string => $state->getLabel())
                    ->sortable(),

                TextColumn::make('method')
                    ->label(__('filament.fields.method'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('reference_id')
                    ->label(__('filament.fields.reference'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.fields.status'))
                    ->options(collect(WithdrawalStatus::cases())->mapWithKeys(fn(WithdrawalStatus $case) => [$case->value => $case->getLabel()])),

                SelectFilter::make('method')
                    ->label(__('filament.fields.method'))
                    ->options([
                        'vodafone_cash' => 'Vodafone Cash',
                        'instapay' => 'InstaPay',
                        'bank_transfer' => 'Bank Transfer',
                    ]),

                \Filament\Tables\Filters\Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label(__('filament.fields.created_from')),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label(__('filament.fields.created_until')),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('approve')
                        ->label(__('filament.options.status.approved'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(Withdrawal $record) => $record->status === WithdrawalStatus::PENDING)
                        ->requiresConfirmation()
                        ->action(function (Withdrawal $record, WithdrawalService $service) {
                            $service->approveWithdrawal($record);
                            Notification::make()
                                ->title('Withdrawal Approved')
                                ->success()
                                ->send();
                        }),

                    Action::make('complete')
                        ->label(__('filament.options.status.active'))
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn(Withdrawal $record) => $record->status === WithdrawalStatus::APPROVED)
                        ->form([
                            \Filament\Forms\Components\TextInput::make('reference_id')
                                ->label(__('filament.fields.reference'))
                                ->placeholder('Enter payment reference ID'),
                        ])
                        ->action(function (Withdrawal $record, array $data, WithdrawalService $service) {
                            $service->completeWithdrawal($record, $data['reference_id'] ?? null);
                            Notification::make()
                                ->title('Withdrawal Completed')
                                ->success()
                                ->send();
                        }),

                    Action::make('reject')
                        ->label(__('filament.options.status.rejected'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(Withdrawal $record) => $record->status === WithdrawalStatus::PENDING || $record->status === WithdrawalStatus::APPROVED)
                        ->form([
                            \Filament\Forms\Components\Textarea::make('reason')
                                ->label(__('filament.fields.description'))
                                ->required(),
                        ])
                        ->action(function (Withdrawal $record, array $data, WithdrawalService $service) {
                            $service->rejectWithdrawal($record, $data['reason']);
                            Notification::make()
                                ->title('Withdrawal Rejected and Account Re-credited')
                                ->danger()
                                ->send();
                        }),

                    ViewAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
