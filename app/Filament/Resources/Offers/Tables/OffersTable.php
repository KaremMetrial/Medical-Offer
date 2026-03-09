<?php

namespace App\Filament\Resources\Offers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->with(['translations', 'provider.translations', 'category.translations']))
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.fields.name'))
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->searchName($search);
                    }),

                TextColumn::make('provider.name')
                    ->label(__('filament.fields.provider'))
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label(__('filament.fields.category'))
                    ->sortable(),

                TextColumn::make('discount_percent')
                    ->label(__('filament.fields.discount_percent'))
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => __('filament.options.status.' . $state))
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'expired' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('start_date')
                    ->label(__('filament.fields.start_date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('filament.fields.end_date'))
                    ->date()
                    ->sortable(),

                IconColumn::make('show_in_home')
                    ->label(__('filament.fields.show_in_home'))
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
