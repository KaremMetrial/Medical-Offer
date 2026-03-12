<?php

namespace App\Filament\Resources\Stories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider.id')
                    ->label(__('filament.fields.provider'))
                    ->formatStateUsing(fn($record) => $record->provider?->name ?? $record->provider_id)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('story_type')
                    ->label(__('filament.fields.story_type'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'image' => 'success',
                        'video' => 'warning',
                    }),

                ImageColumn::make('media_url')
                    ->label(__('filament.fields.media_url'))
                    ->visibility(fn($record) => $record->story_type === 'image')
                    ->disk('public'),

                TextColumn::make('views_count')
                    ->counts('views')
                    ->label(__('filament.fields.views'))
                    ->sortable(),

                TextColumn::make('expiry_time')
                    ->label(__('filament.fields.expiry_time'))
                    ->dateTime()
                    ->sortable()
                    ->color(fn($record) => $record->expiry_time->isPast() ? 'danger' : 'success'),

                TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('active')
                    ->query(fn($query) => $query->where('expiry_time', '>', now()))
                    ->label(__('filament.options.status.active')),
                \Filament\Tables\Filters\Filter::make('expired')
                    ->query(fn($query) => $query->where('expiry_time', '<=', now()))
                    ->label(__('filament.options.status.expired')),
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
