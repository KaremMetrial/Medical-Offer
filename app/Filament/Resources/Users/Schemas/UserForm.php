<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Illuminate\Support\Facades\Hash;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Nationality;

use App\Traits\UploadTrait;

class UserForm
{
    use UploadTrait;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make(__('filament.sections.general'))
                        ->schema([
                            TextInput::make('name')
                                ->label(__('filament.fields.name'))
                                ->required(),
                            TextInput::make('email')
                                ->label(__('filament.fields.email'))
                                ->email()
                                ->unique(ignoreRecord: true),
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
                                ->options(fn() => Country::with('translations')->get()->pluck('name', 'id'))
                                ->searchable()
                                ->live(),
                            Select::make('governorate_id')
                                ->label(__('filament.fields.governorate'))
                                ->relationship('governorate', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn(callable $get) => Governorate::where('country_id', $get('country_id'))->with('translations')->get()->pluck('name', 'id'))
                                ->searchable()
                                ->live(),
                            Select::make('city_id')
                                ->label(__('filament.fields.city'))
                                ->relationship('city', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn(callable $get) => City::where('governorate_id', $get('governorate_id'))->with('translations')->get()->pluck('name', 'id'))
                                ->searchable(),
                            Select::make('gender')
                                ->label(__('filament.fields.gender'))
                                ->options([
                                    'male' => __('filament.options.gender.male'),
                                    'female' => __('filament.options.gender.female'),
                                ])
                                ->required(),
                            Select::make('nationality_id')
                                ->label(__('filament.fields.nationality'))
                                ->relationship('nationality', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn() => Nationality::with('translations')->get()->pluck('name', 'id'))
                                ->searchable()
                                ->live(),
                        ])->columns(3),

                    Section::make(__('filament.fields.password'))
                        ->schema([
                            TextInput::make('password')
                                ->label(__('filament.fields.password'))
                                ->password()
                                ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                ->dehydrated(fn($state) => filled($state))
                                ->required(fn(string $context): bool => $context === 'create'),
                        ]),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.media'))
                        ->schema([
                            FileUpload::make('avatar')
                                ->label(__('filament.fields.image'))
                                ->image()
                                ->disk('public')
                                ->directory('users/avatars'),
                        ]),

                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Select::make('role')
                                ->label(__('filament.fields.role'))
                                ->options(__('filament.options.role'))
                                ->required(),
                            Toggle::make('is_active')
                                ->label(__('filament.fields.is_active'))
                                ->default(true),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
