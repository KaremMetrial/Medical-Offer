<?php

namespace App\Filament\Resources\MemberPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MemberPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['translations', 'country.translations']))
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.fields.name'))
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->searchName($search);
                    }),

                                TextColumn::make('country.name')
                    ->label(__('filament.fields.country'))
                    ->sortable()
                    ->searchable()
                    ->default('-'),

                TextColumn::make('price')
                    ->label(__('filament.fields.price'))
                    ->formatStateUsing(function ($state, $record) {
                        $symbol = $record->country?->currency_symbol ?? '$';
                        $unit   = $record->country?->currency_unit   ?? 'USD';
                        return $symbol . number_format((float) $state, 2) . ' ' . $unit;
                    })
                    ->sortable(),

                TextColumn::make('duration_days')
                    ->label(__('filament.fields.duration_days'))
                    ->sortable(),

                IconColumn::make('is_provider')
                    ->label(__('filament.fields.is_provider'))
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
