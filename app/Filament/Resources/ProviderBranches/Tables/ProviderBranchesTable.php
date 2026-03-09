<?php

namespace App\Filament\Resources\ProviderBranches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProviderBranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->with(['provider.translations', 'country.translations', 'governorate.translations', 'city.translations']))
            ->columns([
                TextColumn::make('name_en')
                    ->label(__('filament.fields.name') . ' (EN)')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('provider.name')
                    ->label(__('filament.fields.provider'))
                    ->sortable(),

                TextColumn::make('city.name')
                    ->label(__('filament.fields.city'))
                    ->sortable(),

                IconColumn::make('is_main')
                    ->label(__('filament.fields.is_main'))
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
