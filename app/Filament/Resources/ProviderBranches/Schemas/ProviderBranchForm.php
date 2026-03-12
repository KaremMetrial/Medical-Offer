<?php

namespace App\Filament\Resources\ProviderBranches\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use App\Models\Provider;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use App\Filament\Components\TranslatableFields;
class ProviderBranchForm
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
                            Select::make('provider_id')
                                ->label(__('filament.fields.provider'))
                                ->relationship('provider', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn() => Provider::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            TextInput::make('phone')
                                ->label(__('filament.fields.phone'))
                                ->tel(),
                        ])->columns(2),

                    Section::make(__('filament.fields.address'))
                        ->schema([
                            Select::make('country_id')
                                ->label(__('filament.fields.country'))
                                ->relationship('country', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn() => Country::all()->pluck('name', 'id'))
                                ->searchable()
                                ->live(),
                            Select::make('governorate_id')
                                ->label(__('filament.fields.governorate'))
                                ->relationship('governorate', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn(callable $get) => Governorate::where('country_id', $get('country_id'))->get()->pluck('name', 'id'))
                                ->searchable()
                                ->live(),
                            Select::make('city_id')
                                ->label(__('filament.fields.city'))
                                ->relationship('city', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn(callable $get) => City::where('governorate_id', $get('governorate_id'))->get()->pluck('name', 'id'))
                                ->searchable(),
                            Textarea::make('address')
                                ->label(__('filament.fields.address'))
                                ->columnSpanFull(),
                            TextInput::make('lat')
                                ->label(__('filament.fields.lat'))
                                ->numeric(),
                            TextInput::make('lng')
                                ->label(__('filament.fields.lng'))
                                ->numeric(),
                        ])->columns(2),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Toggle::make('is_main')
                                ->label(__('filament.fields.is_main'))
                                ->default(false),
                            Toggle::make('is_active')
                                ->label(__('filament.fields.is_active'))
                                ->default(true),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
