<?php

namespace App\Filament\Resources\Banners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Banner;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with('translations'))
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament.fields.title'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->searchTitle($search);
                    })
                    ->formatStateUsing(fn(Banner $record): string => $record->title)
                    ->toggleable(),

                ImageColumn::make('image_path')
                    ->label(__('filament.fields.image_path')),

                TextColumn::make('link_type')
                    ->label(__('filament.fields.link_type'))
                    ->badge(),

                TextColumn::make('link_id')
                    ->label(__('filament.fields.link_id'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('external_url')
                    ->label(__('filament.fields.external_url'))
                    ->searchable(),

                TextColumn::make('start_date')
                    ->label(__('filament.fields.start_date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('filament.fields.end_date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('position')
                    ->label(__('filament.fields.position'))
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label(__('filament.fields.sort_order'))
                    ->numeric()
                    ->sortable(),
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
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
