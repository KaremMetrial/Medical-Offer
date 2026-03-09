<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make(__('filament.sections.general'))
                        ->schema([
                            Select::make('user_id')
                                ->label(__('filament.fields.user'))
                                ->relationship('user', 'name')
                                ->searchable()
                                ->required(),
                            Select::make('provider_id')
                                ->label(__('filament.fields.provider'))
                                ->relationship('provider', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->searchable(),
                            Select::make('offer_id')
                                ->label(__('filament.fields.offer'))
                                ->relationship('offer', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->searchable(),
                        ])->columns(3),

                    Section::make(__('filament.fields.comment'))
                        ->schema([
                            TextInput::make('rating')
                                ->label(__('filament.fields.rating'))
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(5)
                                ->required(),
                            Textarea::make('comment')
                                ->label(__('filament.fields.comment'))
                                ->required()
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Select::make('status')
                                ->label(__('filament.fields.status'))
                                ->options(__('filament.options.status'))
                                ->default('pending')
                                ->required(),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
