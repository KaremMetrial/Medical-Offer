<?php

namespace App\Filament\Resources\Providers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use App\Filament\Components\TranslatableFields;
use App\Models\Country;

class ProviderForm
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
                                'title' => [
                                    'type' => 'text',
                                    'label' => __('filament.fields.title'),
                                    'overrides' => fn($component) => $component->columnSpanFull(),
                                ],
                                'description' => [
                                    'type' => 'textarea',
                                    'label' => __('filament.fields.description'),
                                    'overrides' => fn($component) => $component->columnSpanFull(),
                                ],
                            ]),
                        ]),

                    Section::make(__('filament.sections.general'))
                        ->schema([
                            TextInput::make('phone')
                                ->label(__('filament.fields.phone'))
                                ->tel()
                                ->required(),
                            TextInput::make('experince_years')
                                ->label(__('filament.fields.experince_years'))
                                ->numeric(),
                            Select::make('country_id')
                                ->label(__('filament.fields.country'))
                                ->relationship('country', 'id')
                                ->getOptionLabelFromRecordUsing(fn(Country $record) => $record->name)
                                ->searchable()
                                ->required(),
                            Select::make('categories')
                                ->label(__('filament.sections.categories'))
                                ->relationship('categories', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->multiple()
                                ->preload(),
                        ])->columns(2),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.media'))
                        ->schema([
                            FileUpload::make('logo')
                                ->label(__('filament.fields.logo'))
                                ->image()
                                ->directory('providers/logos'),
                            FileUpload::make('cover')
                                ->label(__('filament.fields.cover'))
                                ->image()
                                ->directory('providers/covers'),
                        ])->columns(2),

                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Select::make('status')
                                ->label(__('filament.fields.status'))
                                ->options(__('filament.options.status'))
                                ->default('pending')
                                ->required(),
                            Toggle::make('is_varified')
                                ->label(__('filament.fields.is_varified'))
                                ->default(false),
                            TextInput::make('views')
                                ->label(__('filament.fields.views'))
                                ->numeric()
                                ->default(0)
                                ->disabled(),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
