<?php

namespace App\Filament\Resources\WalletTransactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\SelectColumn;
use App\Enums\WalletTransactionType;

class WalletTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['user.country']))
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('filament.fields.user'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                BadgeColumn::make('type')
                    ->label(__('filament.fields.type'))
                    ->color(fn (WalletTransactionType $state): string => $state->getColor())
                    ->icon(fn (WalletTransactionType $state): string => $state->getIcon())
                    ->formatStateUsing(fn (WalletTransactionType $state): string => $state->getLabel())
                    ->sortable(),

                TextColumn::make('amount')
                    ->label(__('filament.fields.amount'))
                    ->formatStateUsing(function ($state) {
                        $finalPrice = app(\App\Services\CurrencyService::class)->convert((float) $state, 'USD', 'EGP');
                        return round($finalPrice, 2) . ' EGP';
                    })
                    ->sortable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->formatStateUsing(function ($state) {
                                $finalPrice = app(\App\Services\CurrencyService::class)->convert((float) $state, 'USD', 'EGP');
                                return round($finalPrice, 2) . ' EGP';
                            })
                            ->label(__('filament.wallet_transaction.total_amount')),
                    ]),

                TextColumn::make('balance_after')
                    ->label(__('filament.fields.balance_after'))
                    ->formatStateUsing(function ($state) {
                        $finalPrice = app(\App\Services\CurrencyService::class)->convert((float) $state, 'USD', 'EGP');
                        return round($finalPrice, 2) . ' EGP';
                    })
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('filament.fields.description'))
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reference')
                    ->label(__('filament.fields.reference'))
                    ->searchable()
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
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->label(__('filament.fields.type'))
                    ->options(collect(WalletTransactionType::cases())->mapWithKeys(fn(WalletTransactionType $case) => [$case->value => $case->getLabel()])),
                
                \Filament\Tables\Filters\Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label(__('filament.fields.created_from')),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label(__('filament.fields.created_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                // EditAction::make(),
                // DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}