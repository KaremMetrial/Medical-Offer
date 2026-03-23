<?php

namespace App\Filament\Resources\Complaints\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

class ComplaintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('filament.fields.user'))
                    ->placeholder(__('filament.options.all'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('filament.fields.phone'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('message')
                    ->label(__('filament.fields.comment'))
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state === 'resolved' ? 'success' : 'warning'),

                TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
