<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use App\Filament\Components\TranslatableFields;
use App\Models\Category;

class CategoryForm
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
                            Select::make('parent_id')
                                ->label(__('filament.fields.parent'))
                                ->options(
                                    fn(?Category $record) => Category::query()
                                        ->when($record, fn($query) => $query->where('id', '!=', $record->id))
                                        ->get()
                                        ->pluck('name', 'id')
                                )
                                ->searchable()
                                ->placeholder(__('filament.fields.select_parent')),
                        ])->columns(1),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.media'))
                        ->schema([
                            FileUpload::make('icon')
                                ->label(__('filament.fields.icon'))
                                ->image()
                                ->hiddenLabel(),
                        ]),

                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Toggle::make('is_active')
                                ->label(__('filament.fields.is_active'))
                                ->default(true),

                            Toggle::make('is_show')
                                ->label(__('filament.fields.is_show'))
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
