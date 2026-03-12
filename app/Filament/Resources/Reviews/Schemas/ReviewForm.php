<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Models\Provider;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use App\Models\Offer;
use App\Models\User;

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
                                ->relationship('user', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn() => User::whereNotIn('role', ['admin', 'super_admin'])->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            Select::make('provider_id')
                                ->label(__('filament.fields.provider'))
                                ->relationship('provider', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn() => Provider::all()->pluck('name', 'id'))
                                ->searchable(),
                            Select::make('offer_id')
                                ->label(__('filament.fields.offer'))
                                ->relationship('offer', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                                ->options(fn() => Offer::all()->pluck('name', 'id'))
                                ->searchable(),
                        ])->columns(3),

                    Section::make(__('filament.fields.comment'))
                        ->schema([
                            Select::make('rating')
                                ->label(__('filament.fields.rating'))
                                ->options([
                                    1 => '1',
                                    2 => '2',
                                    3 => '3',
                                    4 => '4',
                                    5 => '5',
                                ])
                                ->default(5)
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
                                ->options([
                                    'pending' => 'Pending',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                ])
                                ->default('approved')
                                ->required(),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
