<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use App\Filament\Components\TranslatableFields;

use App\Traits\UploadTrait;

class BannerForm
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
                                'title' => [
                                    'type' => 'text',
                                    'label' => __('filament.fields.title'),
                                    'overrides' => fn($component) => $component->columnSpanFull(),
                                ],
                            ]),
                        ]),

                    Section::make(__('filament.sections.link_info'))
                        ->schema([
                            Select::make('link_type')
                                ->options([
                                    'offer' => 'Offer',
                                    'provider' => 'Provider',
                                    'category' => 'Category',
                                    'external' => 'External',
                                ])
                                ->default('external')
                                ->required()
                                ->live(),

                            Select::make('link_id')
                                ->label(__('filament.fields.link_id'))
                                ->options(function ($get) {
                                    return match ($get('link_type')) {
                                        'offer' => \App\Models\Offer::get()->pluck('name', 'id'),
                                        'provider' => \App\Models\Provider::get()->pluck('name', 'id'),
                                        'category' => \App\Models\Category::get()->pluck('name', 'id'),
                                        default => [],
                                    };
                                })
                                ->searchable()
                                ->visible(fn($get) => in_array($get('link_type'), ['offer', 'provider', 'category'])),

                            TextInput::make('external_url')
                                ->label(__('filament.fields.external_url'))
                                ->url()
                                ->visible(fn($get) => $get('link_type') === 'external'),
                        ])->columns(2),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.media'))
                        ->schema([
                            FileUpload::make('image_path')
                                ->image()
                                ->hiddenLabel()
                                ->disk('public')
                                ->directory('banners')
                                ->required(),
                        ]),

                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Toggle::make('is_active')
                                ->label(__('filament.fields.is_active'))
                                ->default(true),

                            TextInput::make('position')
                                ->label(__('filament.fields.position'))
                                ->required()
                                ->default('home_top'),

                            TextInput::make('sort_order')
                                ->label(__('filament.fields.sort_order'))
                                ->required()
                                ->numeric()
                                ->default(0),

                            DatePicker::make('start_date')
                                ->label(__('filament.fields.start_date')),

                            DatePicker::make('end_date')
                                ->label(__('filament.fields.end_date')),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
