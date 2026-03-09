<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use App\Filament\Components\TranslatableFields;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make(__('filament.sections.translations'))
                        ->schema([
                            TranslatableFields::make([
                                'name' => [
                                    'type' => 'text',
                                    'label' => __('filament.fields.name'),
                                    'overrides' => fn($component) => $component->columnSpanFull(),
                                ],
                            ]),
                        ]),

                    Section::make(__('filament.sections.general'))
                        ->schema([
                            TextInput::make('phone_code')
                                ->label(__('filament.fields.phone_code'))
                                ->maxLength(10),
                            TextInput::make('currency_symbol')
                                ->label(__('filament.fields.currency_symbol'))
                                ->maxLength(10),
                            TextInput::make('currency_name')
                                ->label(__('filament.fields.currency_name'))
                                ->maxLength(20),
                            TextInput::make('currency_unit')
                                ->label(__('filament.fields.currency_unit'))
                                ->maxLength(10),
                            TextInput::make('currency_factor')
                                ->label(__('filament.fields.currency_factor'))
                                ->numeric()
                                ->default(1),
                        ])->columns(2),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            FileUpload::make('flag')
                                ->label(__('filament.fields.flag'))
                                ->image(),
                            Select::make('timezone')
                                ->label(__('filament.fields.timezone'))
                                ->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list()))
                                ->searchable(),
                            Toggle::make('is_active')
                                ->label(__('filament.fields.is_active'))
                                ->default(true),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
