<?php

namespace App\Filament\Resources\Offers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use App\Filament\Components\TranslatableFields;
use Filament\Forms\Components\FileUpload;
use App\Models\Provider;
use App\Models\Category;
use App\Traits\UploadTrait;

class OfferForm
{
    use UploadTrait;

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
                                ->options(fn() => Provider::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            Select::make('category_id')
                                ->label(__('filament.fields.category'))
                                ->relationship('category', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn() => Category::all()->pluck('name', 'id'))
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
                            Repeater::make('images')
                                ->label(__('filament.sections.media'))
                                ->relationship('images')
                                ->schema([
                                    FileUpload::make('path')
                                        ->label(__('filament.fields.image'))
                                        ->image()
                                        ->directory('offers/images')
                                        ->disk('public')
                                        ->required(),
                                ])
                                ->orderColumn('sort_order')
                                ->grid(2)
                                ->columnSpanFull(),
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
