<?php

namespace App\Filament\Resources\ManualNotifications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Models\User;

use App\Enums\ManualNotificationTarget;

class ManualNotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament.groups.details'))
                ->schema([
                    Grid::make(1)->schema([
                        Select::make('target_type')
                            ->label(__('filament.fields.target_type'))
                            ->options(ManualNotificationTarget::class)
                            ->required()
                            ->live()
                            ->default(ManualNotificationTarget::ALL),

                        Select::make('user_id')
                            ->label(__('filament.fields.user'))
                            ->options(fn() => User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->visible(fn(callable $get) => $get('target_type') === ManualNotificationTarget::SPECIFIC->value)
                            ->required(fn(callable $get) => $get('target_type') === ManualNotificationTarget::SPECIFIC->value),


                        TextInput::make('title')
                            ->label(__('filament.fields.title'))
                            ->required()
                            ->maxLength(255),

                        Textarea::make('message')
                            ->label(__('filament.fields.message'))
                            ->required()
                            ->maxLength(1000),
                    ]),
                ])->columnSpanFull(),
        ]);
    }
}
