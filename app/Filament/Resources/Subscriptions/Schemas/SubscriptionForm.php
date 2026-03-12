<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\MemberPlan;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;

class SubscriptionForm
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
                                ->relationship('user', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn() => User::where('is_active', true)->whereNotIn('role', ['admin', 'super_admin'])->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            Select::make('plan_id')
                                ->label(__('filament.fields.plan'))
                                ->relationship('plan', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn() => MemberPlan::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])->columns(2),

                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            DatePicker::make('start_at')
                                ->label(__('filament.fields.start_at'))
                                ->required(),
                            DatePicker::make('end_at')
                                ->label(__('filament.fields.end_at'))
                                ->required(),
                        ])->columns(2),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Select::make('status')
                                ->label(__('filament.fields.status'))
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'expired' => 'Expired',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->default('active')
                                ->required(),
                            Select::make('payment_status')
                                ->label(__('filament.fields.payment_status'))
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
