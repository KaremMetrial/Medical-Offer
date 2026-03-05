<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Filament\Components\TranslatableFields;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                                    Section::make(__('filament.sections.translations'))
                        ->schema([
                            TranslatableFields::make([
                                'name' => [
                                    'type' => 'text',
                                    'label' => __('filament.fields.name'),
                                    'overrides' => fn($component) => $component->columnSpanFull(),
                                ],
                            ])
                        ])
                        ->columns(1)
                        ->columnSpanFull(),

                FileUpload::make('image_path')
                    ->image()
                    ->required(),
                Select::make('link_type')
                    ->options([
            'offer' => 'Offer',
            'provider' => 'Provider',
            'category' => 'Category',
            'external' => 'External',
        ])
                    ->default('external')
                    ->required(),
                TextInput::make('link_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('external_url')
                    ->url()
                    ->default(null),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
                TextInput::make('position')
                    ->required()
                    ->default('home_top'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
