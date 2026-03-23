<?php

namespace App\Filament\Resources\ManualNotifications\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;

class ManualNotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament.fields.title'))
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('target_type')
                    ->label(__('filament.fields.target_type'))
                    ->color(fn (\App\Enums\ManualNotificationTarget $state): string => $state->getColor())
                    ->formatStateUsing(fn (\App\Enums\ManualNotificationTarget $state): string => $state->getLabel())
                    ->badge(),

                
                TextColumn::make('user.name')
                    ->label(__('filament.fields.user'))
                    ->placeholder(__('filament.options.target_type.all-users'))
                    ->searchable(),
                
                TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
