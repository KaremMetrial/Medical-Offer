<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make(__('filament.sections.general'))
                        ->schema([
                            TextInput::make('payable_type')
                                ->label('Payable Type')
                                ->required(),
                            TextInput::make('payable_id')
                                ->label('Payable ID')
                                ->numeric()
                                ->required(),
                            Select::make('provider_id')
                                ->label(__('filament.fields.provider'))
                                ->relationship('provider', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->searchable(),
                        ])->columns(3),

                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            TextInput::make('amount')
                                ->label(__('filament.fields.amount'))
                                ->numeric()
                                ->prefix('$')
                                ->required(),
                            TextInput::make('method')
                                ->label(__('filament.fields.method'))
                                ->required(),
                            TextInput::make('provider_ref')
                                ->label(__('filament.fields.provider_ref')),
                        ])->columns(3),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Select::make('status')
                                ->label(__('filament.fields.status'))
                                ->options([
                                    'pending' => 'Pending',
                                    'paid' => 'Paid',
                                    'failed' => 'Failed',
                                    'refunded' => 'Refunded',
                                ])
                                ->default('pending')
                                ->required(),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
