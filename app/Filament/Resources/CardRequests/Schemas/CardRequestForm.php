<?php

namespace App\Filament\Resources\CardRequests\Schemas;

use App\Models\City;
use App\Models\Governorate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class CardRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.groups.details'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('user_id')
                                ->label(__('filament.fields.user'))
                                ->relationship('user', 'name')
                                ->searchable()
                                ->required(),
                            
                            Select::make('status')
                                ->label(__('filament.fields.status'))
                                ->options(\App\Enums\CardRequestStatus::class)
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('receiver_name')
                                ->label(__('filament.fields.receiver_name')),
                            
                            TextInput::make('receiver_phone')
                                ->label(__('filament.fields.receiver_phone'))
                                ->tel(),
                        ]),

                        Textarea::make('address')
                            ->label(__('filament.fields.address'))
                            ->required()
                            ->columnSpanFull(),
                        
                        Grid::make(2)->schema([
                            Select::make('governorate_id')
                                ->label(__('filament.fields.governorate'))
                                ->options(Governorate::all()->pluck('name', 'id'))
                                ->reactive()
                                ->required(),
                            
                            Select::make('city_id')
                                ->label(__('filament.fields.city'))
                                ->options(function (callable $get) {
                                    $governorateId = $get('governorate_id');
                                    if (!$governorateId) {
                                        return [];
                                    }
                                    return City::where('governorate_id', $governorateId)->get()->pluck('name', 'id');
                                })
                                ->required(),
                        ]),
                    ]),

                Section::make(__('filament.groups.financials'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('issuance_fee')
                                ->label(__('filament.fields.issuance_fee'))
                                ->numeric()
                                ->suffix('EGP')
                                ->formatStateUsing(function ($state) {
                                    $currencyService = app(\App\Services\CurrencyService::class);
                                    $systemBase = config('settings.currency.system_base', 'USD');
                                    $amountInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                                    return number_format($amountInEGP, 2) . ' EGP';
                                })
                                ->required(),
                            
                            TextInput::make('delivery_fee')
                                ->label(__('filament.fields.delivery_fee'))
                                ->numeric()
                                ->suffix('EGP')
                                ->formatStateUsing(function ($state) {
                                    $currencyService = app(\App\Services\CurrencyService::class);
                                    $systemBase = config('settings.currency.system_base', 'USD');
                                    $amountInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                                    return number_format($amountInEGP, 2) . ' EGP';
                                })
                                ->required(),
                            
                            TextInput::make('total_amount')
                                ->label(__('filament.fields.total_amount'))
                                ->numeric()
                                ->suffix('EGP')
                                ->formatStateUsing(function ($state) {
                                    $currencyService = app(\App\Services\CurrencyService::class);
                                    $systemBase = config('settings.currency.system_base', 'USD');
                                    $amountInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                                    return number_format($amountInEGP, 2) . ' EGP';
                                })
                                ->required(),
                        ]),
                    ])->collapsed(),
            ]);
    }
}
