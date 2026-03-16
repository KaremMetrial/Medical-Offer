<?php

namespace App\Filament\Resources\Nationalities\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use App\Filament\Components\TranslatableFields;
use Filament\Forms\Components\Toggle;

class NationalityForm
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
                ])->columnSpan(3),
                Group::make([
                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Toggle::make('is_active')
                                ->label(__('filament.fields.is_active'))
                                ->default(true),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
