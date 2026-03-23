<?php

namespace App\Filament\Resources\Withdrawals\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Enums\WithdrawalStatus;

class WithdrawalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament.groups.details'))
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('user_id')
                            ->label(__('filament.fields.user'))
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable(),
                        
                        Select::make('status')
                            ->label(__('filament.fields.status'))
                            ->options(WithdrawalStatus::class)
                            ->required(),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('method')
                            ->label(__('filament.fields.method'))
                            ->required(),
                        
                        TextInput::make('reference_id')
                            ->label(__('filament.fields.reference')),
                    ]),

                    Textarea::make('rejection_reason')
                        ->label(__('filament.fields.description'))
                        ->columnSpanFull(),
                ]),

            Section::make(__('filament.groups.financials'))
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('amount')
                            ->label(__('filament.fields.amount'))
                            ->numeric()
                            ->suffix('EGP')
                            ->formatStateUsing(function ($state) {
                                $currencyService = app(\App\Services\CurrencyService::class);
                                $systemBase = config('settings.currency.system_base', 'USD');
                                $amountInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                                return number_format($amountInEGP, 2);
                            })
                            ->required(),
                        
                        TextInput::make('fee')
                            ->label(__('filament.fields.fees'))
                            ->numeric()
                            ->suffix('EGP')
                            ->formatStateUsing(function ($state) {
                                $currencyService = app(\App\Services\CurrencyService::class);
                                $systemBase = config('settings.currency.system_base', 'USD');
                                $amountInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                                return number_format($amountInEGP, 2);
                            })
                            ->default(0),
                        
                        TextInput::make('net_amount')
                            ->label(__('filament.fields.total_amount'))
                            ->numeric()
                            ->suffix('EGP')
                            ->formatStateUsing(function ($state) {
                                $currencyService = app(\App\Services\CurrencyService::class);
                                $systemBase = config('settings.currency.system_base', 'USD');
                                $amountInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                                return number_format($amountInEGP, 2);
                            })
                            ->required(),
                    ]),
                ])->collapsed(),
        ]);
    }
}

