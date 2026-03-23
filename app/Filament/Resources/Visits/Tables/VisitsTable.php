<?php

namespace App\Filament\Resources\Visits\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;


class VisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('filament.fields.user'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('companion.name')
                    ->label(__('filament.fields.relationship'))
                    ->placeholder(__('filament.options.all'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('provider.name')
                    ->label(__('filament.fields.provider'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('visit_date')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('paid_amount')
                    ->label(__('filament.fields.amount'))
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state === 'completed' ? 'success' : 'danger'),
            ])
            ->actions([
                ViewAction::make(),
                // EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
