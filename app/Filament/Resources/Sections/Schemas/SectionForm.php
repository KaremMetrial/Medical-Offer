<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Components\Group;
use App\Filament\Components\TranslatableFields;
use App\Enums\SectionType;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    SectionComponent::make(__('filament.sections.translations'))
                        ->schema([
                            TranslatableFields::make([
                                'name' => [
                                    'type' => 'text',
                                    'label' => __('filament.fields.name'),
                                    'overrides' => fn($component) => $component->columnSpanFull(),
                                ],
                            ]),
                        ]),
                ])->columnSpan(3),

                Group::make([
                    SectionComponent::make(__('filament.sections.media'))
                        ->schema([
                            Select::make('type')
                                ->label(__('filament.fields.type'))
                                ->options(collect(SectionType::cases())->mapWithKeys(fn(SectionType $case) => [$case->value => $case->getLabel()]))
                                ->required(),

                            FileUpload::make('icon')
                                ->label(__('filament.fields.icon'))
                                ->image()
                                ->disk('public')
                                ->directory('sections')
                                ->hiddenLabel(),
                        ]),

                    SectionComponent::make(__('filament.sections.settings'))
                        ->schema([
                            Toggle::make('is_active')
                                ->label(__('filament.fields.is_active'))
                                ->default(true),

                            TextInput::make('sort_order')
                                ->label(__('filament.fields.sort_order'))
                                ->required()
                                ->numeric()
                                ->default(0),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
