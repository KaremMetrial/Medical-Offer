<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use App\Models\Provider;
use App\Models\User;
use App\Models\Subscription;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make(__('filament.sections.general'))
                        ->schema([
                            Select::make('payable_type')
                                ->label('Payable Type')
                                ->options([
                                    User::class => 'User',
                                    Subscription::class => 'Subscription',
                                ])
                                ->live()
                                ->required(),
                            Select::make('payable_id')
                                ->label('Payable ID')
                                ->options(function ($get) {
                                    $type = $get('payable_type');
                                    if ($type === User::class) {
                                        return User::pluck('name', 'id');
                                    }
                                    if ($type === Subscription::class) {
                                        return Subscription::with(['user', 'plan.translations'])
                                            ->get()
                                            ->mapWithKeys(function ($subscription) {
                                                $userName = $subscription->user?->name ?? 'Unknown';
                                                $planName = $subscription->plan?->name ?? 'Unknown Plan';
                                                return [$subscription->id => "{$userName} - {$planName} (#{$subscription->id})"];
                                            });
                                    }
                                    return [];
                                })
                                ->searchable()
                                ->required(),
                            Select::make('provider_id')
                                ->label(__('filament.fields.provider'))
                                ->relationship('provider', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn() => Provider::all()->pluck('name', 'id'))
                                ->searchable()
                                ->live(),
                        ])->columns(3),

                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            TextInput::make('amount')
                                ->label(__('filament.fields.amount'))
                                ->numeric()
                                ->prefix(function ($get) {
                                    $providerId = $get('provider_id');
                                    if ($providerId) {
                                        $provider = Provider::with('country')->find($providerId);
                                        return $provider?->country?->currency_symbol ?? '$';
                                    }
                                    return '$';
                                })
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
