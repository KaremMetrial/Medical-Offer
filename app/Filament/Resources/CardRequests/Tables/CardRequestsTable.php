<?php

namespace App\Filament\Resources\CardRequests\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class CardRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->with(['governorate.country']))
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('filament.fields.user'))
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->badge()
                    ->color(fn (\App\Enums\CardRequestStatus $state): string => match ($state->value) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'prepared' => 'info',
                        'shipped' => 'warning',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (\App\Enums\CardRequestStatus $state): string => $state->getLabel()),

                TextColumn::make('receiver_name')
                    ->label(__('filament.fields.receiver_name'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('governorate.name')
                    ->label(__('filament.fields.governorate'))
                    ->sortable(),

                TextColumn::make('city.name')
                    ->label(__('filament.fields.city'))
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label(__('filament.fields.total_amount'))
                    ->formatStateUsing(function ($state) {
                        $currencyService = app(\App\Services\CurrencyService::class);
                        $systemBase = config('settings.currency.system_base', 'USD');
                        $amountInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                        return number_format($amountInEGP, 2) . ' EGP';
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.fields.status'))
                    ->options(\App\Enums\CardRequestStatus::class),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
