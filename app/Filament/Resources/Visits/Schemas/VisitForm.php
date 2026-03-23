<?php

namespace App\Filament\Resources\Visits\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Models\User;
use App\Models\Provider;

class VisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament.groups.details'))
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('user_id')
                            ->label(__('filament.fields.user'))
                            ->options(User::whereNull('parent_user_id')->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live(),

                        Select::make('companion_id')
                            ->label(__('filament.fields.relationship'))
                            ->options(fn (callable $get) => User::where('parent_user_id', $get('user_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->placeholder(__('filament.options.all')),

                        Select::make('provider_id')
                            ->label(__('filament.fields.provider'))
                            ->options(Provider::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        DateTimePicker::make('visit_date')
                            ->label(__('filament.fields.created_at'))
                            ->required()
                            ->default(now()),
                    ]),
                ]),

            Section::make(__('filament.fields.services'))

                ->label(__('filament.fields.description'))
                ->schema([
                    Repeater::make('services')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('name')
                                    ->label(__('filament.fields.name'))
                                    ->required(),
                                TextInput::make('discount')
                                    ->label(__('filament.fields.discount_percent'))
                                    ->numeric()
                                    ->suffix('%'),
                            ]),
                            TextInput::make('description')
                                ->label(__('filament.fields.description')),
                        ])
                        ->columnSpanFull(),
                ]),

            Section::make(__('filament.groups.financials'))
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('paid_amount')
                            ->label(__('filament.fields.amount'))
                            ->numeric()
                            ->required(),
                        TextInput::make('discount_amount')
                            ->label(__('filament.fields.total_amount'))
                            ->numeric()
                            ->default(0),
                    ]),
                ]),
        ]);
    }
}
