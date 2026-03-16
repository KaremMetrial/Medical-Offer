<?php

namespace App\Filament\Resources\MemberPlans\Schemas;

use App\Models\Country;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Filament\Components\TranslatableFields;

class MemberPlanForm
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
                                'label' => [
                                    'type' => 'text',
                                    'label' => __('filament.fields.label'),
                                    'overrides' => fn($component) => $component->columnSpanFull(),
                                ],
                                'feature' => [
                                    'type' => 'markdown_editor',
                                    'label' => __('filament.fields.features'),
                                    'overrides' => fn($component) => $component->columnSpanFull(),
                                ],
                            ]),
                        ]),

                    Section::make(__('filament.sections.general'))
                        ->schema([
                            Select::make('country_id')
                                ->label(__('filament.fields.country'))
                                ->options(fn() => Country::all()->pluck('name', 'id'))
                                ->searchable()
                                ->nullable()
                                ->required()
                                ->live()
                                ->columnSpanFull(),
                            TextInput::make('price')
                                ->label(__('filament.fields.price'))
                                ->numeric()
                                ->prefix(function ($get) {
                                    $countryId = $get('country_id');
                                    if ($countryId) {
                                        $country = Country::find($countryId);
                                        return $country?->currency_symbol ?? '$';
                                    }
                                    return '$';
                                })
                                ->required(),
                            TextInput::make('duration_days')
                                ->label(__('filament.fields.duration_days'))
                                ->numeric()
                                ->minValue(1)
                                ->required(),
                            TextInput::make('features_json.number_of_buddies')
                                ->label(__('filament.fields.number_of_buddies'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->columnSpanFull(),
                            TextInput::make('features_json.number_of_providers')
                                ->label(__('filament.fields.number_of_providers'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->columnSpanFull(),
                            TextInput::make('features_json.number_of_visits')
                                ->label(__('filament.fields.number_of_visits'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->columnSpanFull(),
                            TextInput::make('features_json.discount_percentage')
                                ->label(__('filament.fields.discount_percentage'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->columnSpanFull(),
                        ])->columns(2),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Toggle::make('is_provider')
                                ->label(__('filament.fields.is_provider'))
                                ->default(false),
                            Toggle::make('is_active')
                                ->label(__('filament.fields.is_active'))
                                ->default(true),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
