<?php

namespace App\Filament\Resources\MemberPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Services\CurrencyService;
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
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('country', function ($q) use ($search) {
                            $q->searchName($search);
                        });
                    })
                    ->default('-'),

                TextColumn::make('price')
                    ->label(__('filament.fields.price'))
                    ->formatStateUsing(function ($state) {
                        $finalPrice = app(\App\Services\CurrencyService::class)->convert((float) $state, 'USD', 'EGP');
                        return number_format($finalPrice, 2) . ' EGP';
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
