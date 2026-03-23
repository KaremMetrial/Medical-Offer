<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['payable']))
            ->columns([
                TextColumn::make('payable_type')
                    ->label('Type')
                    ->formatStateUsing(fn(string $state): string => class_basename($state))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('payable.name')
                    ->label('Target')
                    ->description(function ($record) {
                        if ($record->payable instanceof \App\Models\Subscription) {
                            $userName = $record->payable->user?->name ?? 'Unknown';
                            $planName = $record->payable->plan?->name ?? 'Unknown Plan';
                            return "{$userName} - {$planName}";
                        }
                        return null;
                    })
                    ->default(fn($record) => $record->payable?->name ?? $record->payable?->email ?? '-')
                    ->searchable(),

                TextColumn::make('amount')
                    ->label(__('filament.fields.amount'))
                    ->formatStateUsing(function ($state) {
                        $finalPrice = app(\App\Services\CurrencyService::class)->convert((float) $state, 'USD', 'EGP');
                        return number_format($finalPrice, 2) . ' EGP';
                    })
                    ->sortable(),

                TextColumn::make('method')
                    ->label(__('filament.fields.method'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('provider_ref')
                    ->label(__('filament.fields.provider_ref'))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
