<?php

namespace App\Filament\Resources\MemberPlans\Schemas;

use App\Models\Country;
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
                            ]),
                        ]),

                    Section::make(__('filament.sections.general'))
                        ->schema([
                            Select::make('country_id')
                                ->label(__('filament.fields.country'))
                                ->options(fn() => Country::with('translations')->get()->pluck('name', 'id'))
                                ->searchable()
                                ->nullable()
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
                                ->required(),
                            TagsInput::make('features_json')
                                ->label(__('filament.fields.features'))
                                ->placeholder(__('filament.fields.add_feature'))
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
