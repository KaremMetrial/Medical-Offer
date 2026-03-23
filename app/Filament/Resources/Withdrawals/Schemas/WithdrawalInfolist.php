<?php

namespace App\Filament\Resources\Withdrawals\Schemas;


use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Enums\WithdrawalStatus;


class WithdrawalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make(__('filament.groups.details'))
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('user.name')
                            ->label(__('filament.fields.user')),
                        
                        TextEntry::make('status')
                            ->label(__('filament.fields.status'))
                            ->badge()
                            ->color(fn (WithdrawalStatus $state): string => $state->getColor())
                            ->formatStateUsing(fn (WithdrawalStatus $state): string => $state->getLabel()),
                    ]),

                    Grid::make(2)->schema([
                        TextEntry::make('method')
                            ->label(__('filament.fields.method')),
                        
                        TextEntry::make('reference_id')
                            ->label(__('filament.fields.reference')),
                    ]),

                    TextEntry::make('rejection_reason')
                        ->label(__('filament.fields.description'))
                        ->columnSpanFull(),
                ]),

            Section::make(__('filament.groups.financials'))
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('amount')
                            ->label(__('filament.fields.amount'))
                            ->formatStateUsing(function ($state) {
                                $currencyService = app(\App\Services\CurrencyService::class);
                                $systemBase = config('settings.currency.system_base', 'USD');
                                $amountInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                                return number_format($amountInEGP, 2) . ' EGP';
                            }),
                        
                        TextEntry::make('fee')
                            ->label(__('filament.fields.fees'))
                            ->formatStateUsing(function ($state) {
                                $currencyService = app(\App\Services\CurrencyService::class);
                                $systemBase = config('settings.currency.system_base', 'USD');
                                $amountInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                                return number_format($amountInEGP, 2) . ' EGP';
                            }),
                        
                        TextEntry::make('net_amount')
                            ->label(__('filament.fields.total_amount'))
                            ->formatStateUsing(function ($state) {
                                $currencyService = app(\App\Services\CurrencyService::class);
                                $systemBase = config('settings.currency.system_base', 'USD');
                                $amountInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                                return number_format($amountInEGP, 2) . ' EGP';
                            }),
                    ]),
                ]),
        ]);
    }
}
