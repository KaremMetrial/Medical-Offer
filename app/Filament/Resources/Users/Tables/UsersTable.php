<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['country', 'governorate', 'city']))
            ->columns([
                ImageColumn::make('avatar')
                    ->label(__('filament.fields.image'))
                    ->disk('public')
                    ->circular(),

                TextColumn::make('name')
                    ->label(__('filament.fields.name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('filament.fields.email'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('role')
                    ->label(__('filament.fields.role'))
                    ->badge(),

                IconColumn::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->boolean(),

                TextColumn::make('city.name')
                    ->label(__('filament.fields.city'))
                    ->sortable()
                    ->searchable(),

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
                DeleteAction::make()
                    ->hidden(fn(\App\Models\User $record) => $record->role === 'super_admin' || $record->id === auth()->id()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $records->filter(fn($record) => $record->role !== 'super_admin' && $record->id !== auth()->id())
                                ->each->delete();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
