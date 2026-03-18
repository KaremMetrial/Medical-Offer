<?php

namespace App\Filament\Resources\WalletTransactions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Enums\WalletTransactionType;

class WalletTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make(__('filament.sections.transaction_details'))
                        ->schema([
                            Select::make('user_id')
                                ->label(__('filament.fields.user'))
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn ($set, $get) => self::updateBalanceAfter($set, $get)),
                            
                            Select::make('type')
                                ->label(__('filament.fields.type'))
                                ->options(collect(WalletTransactionType::cases())->mapWithKeys(fn(WalletTransactionType $case) => [$case->value => $case->getLabel()]))
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn ($set, $get) => self::updateBalanceAfter($set, $get)),

                            TextInput::make('amount')
                                ->label(__('filament.fields.amount'))
                                ->numeric()
                                ->minValue(0)
                                ->step(0.01)
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn ($set, $get) => self::updateBalanceAfter($set, $get))
                                ->prefix(function ($get) {
                                    $userId = $get('user_id');
                                    if ($userId) {
                                        $user = \App\Models\User::find($userId);
                                        return $user?->country?->currency_symbol ?? '$';
                                    }
                                    return '$';
                                })
                                ->formatStateUsing(function ($state, $get) {
                                    $userId = $get('user_id');
                                    if ($state !== null && $userId) {
                                        $user = \App\Models\User::find($userId);
                                        $country = $user?->country;
                                        if ($country) {
                                            $factor = (float)($country->currency_factor ?: 1);
                                            $decimals = $factor == 1000 ? 3 : 2;
                                            $converted = app(\App\Services\CurrencyService::class)->convert((float)$state, 'USD', $country->currency_unit ?? 'USD');
                                            return round($converted, $decimals);
                                        }
                                    }
                                    return $state;
                                })
                                ->dehydrateStateUsing(function ($state, $get) {
                                    $userId = $get('user_id');
                                    if ($state !== null && $userId) {
                                        $user = \App\Models\User::find($userId);
                                        $country = $user?->country;
                                        if ($country) {
                                            $systemBase = config('settings.currency.system_base', 'USD');
                                            return app(\App\Services\CurrencyService::class)->convert((float)$state, $country->currency_unit ?? $systemBase, $systemBase);
                                        }
                                    }
                                    return $state;
                                }),

                            TextInput::make('balance_after')
                                ->label(__('filament.fields.balance_after'))
                                ->numeric()
                                ->minValue(0)
                                ->step(0.01)
                                ->required()
                                ->readOnly()
                                ->prefix(function ($get) {
                                    $userId = $get('user_id');
                                    if ($userId) {
                                        $user = \App\Models\User::find($userId);
                                        return $user?->country?->currency_symbol ?? '$';
                                    }
                                    return '$';
                                })
                                ->formatStateUsing(function ($state, $get) {
                                    $userId = $get('user_id');
                                    if ($state !== null && $userId) {
                                        $user = \App\Models\User::find($userId);
                                        $country = $user?->country;
                                        if ($country) {
                                            $factor = (float)($country->currency_factor ?: 1);
                                            $decimals = $factor == 1000 ? 3 : 2;
                                            $converted = app(\App\Services\CurrencyService::class)->convert((float)$state, 'USD', $country->currency_unit ?? 'USD');
                                            return round($converted, $decimals);
                                        }
                                    }
                                    return $state;
                                })
                                ->dehydrateStateUsing(function ($state, $get) {
                                    $userId = $get('user_id');
                                    if ($state !== null && $userId) {
                                        $user = \App\Models\User::find($userId);
                                        $country = $user?->country;
                                        if ($country) {
                                            $systemBase = config('settings.currency.system_base', 'USD');
                                            return app(\App\Services\CurrencyService::class)->convert((float)$state, $country->currency_unit ?? $systemBase, $systemBase);
                                        }
                                    }
                                    return $state;
                                }),

                            Textarea::make('description')
                                ->label(__('filament.fields.description'))
                                ->rows(3),

                            TextInput::make('reference')
                                ->label(__('filament.fields.reference'))
                                ->maxLength(255),
                        ])->columns(2),
                ])->columnSpan(3),
            ]);
    }

    public static function updateBalanceAfter($set, $get)
    {
        $userId = $get('user_id');
        $type = $get('type');
        $amount = $get('amount'); // This is local currency value

        if (!$userId || !$amount || !$type) {
            return;
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return;
        }

        $country = $user->country;
        $unit = $country?->currency_unit ?? 'USD';
        $systemBase = config('settings.currency.system_base', 'USD');
        $currencyService = app(\App\Services\CurrencyService::class);

        // Convert the input amount to base currency (USD)
        $amountInBase = $currencyService->convert((float)$amount, $unit, $systemBase);

        $currentBalance = (float) $user->balance;

        if ($type === WalletTransactionType::CREDIT->value) {
            $balanceAfterBase = $currentBalance + $amountInBase;
        } else {
            $balanceAfterBase = $currentBalance - $amountInBase;
        }

        // Convert the calculated balance_after back to local currency for display
        $balanceAfterLocal = $currencyService->convert($balanceAfterBase, $systemBase, $unit);
        
        $factor = (float)($country?->currency_factor ?: 1);
        $decimals = $factor == 1000 ? 3 : 2;
        
        $set('balance_after', round($balanceAfterLocal, $decimals));
    }
}