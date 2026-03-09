<?php

namespace App\Filament\Resources\Offers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use App\Filament\Components\TranslatableFields;

class OfferForm
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
                                'description' => [
                                    'type' => 'textarea',
                                    'label' => __('filament.fields.description'),
                                    'overrides' => fn($component) => $component->columnSpanFull(),
                                ],
                                'terms' => [
                                    'type' => 'textarea',
                                    'label' => __('filament.fields.terms'),
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
                                ->searchable()
                                ->required(),
                            Select::make('category_id')
                                ->label(__('filament.fields.category'))
                                ->relationship('category', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->searchable()
                                ->required(),
                            TextInput::make('discount_percent')
                                ->label(__('filament.fields.discount_percent'))
                                ->numeric()
                                ->suffix('%')
                                ->required(),
                        ])->columns(2),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.media'))
                        ->schema([
                            FileUpload::make('images')
                                ->label(__('filament.sections.media'))
                                ->multiple()
                                ->directory('offers/images')
                                ->relationship('images', 'path'),
                        ]),

                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Select::make('status')
                                ->label(__('filament.fields.status'))
                                ->options(__('filament.options.status'))
                                ->default('published')
                                ->required(),
                            DatePicker::make('start_date')
                                ->label(__('filament.fields.start_date')),
                            DatePicker::make('end_date')
                                ->label(__('filament.fields.end_date')),
                            Toggle::make('show_in_home')
                                ->label(__('filament.fields.show_in_home'))
                                ->default(false),
                            TextInput::make('sort_order')
                                ->label(__('filament.fields.sort_order'))
                                ->numeric()
                                ->default(0),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
