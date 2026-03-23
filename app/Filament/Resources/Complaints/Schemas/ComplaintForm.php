<?php

namespace App\Filament\Resources\Complaints\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Models\User;

class ComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament.groups.details'))
                ->schema([
                    Grid::make(1)->schema([
                        Select::make('user_id')
                            ->label(__('filament.fields.user'))
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->disabled(), // Admins shouldn't change the user usually

                        TextInput::make('phone')
                            ->label(__('filament.fields.phone'))
                            ->required()
                            ->maxLength(20),

                        Textarea::make('message')
                            ->label(__('filament.fields.comment'))
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label(__('filament.fields.status'))
                            ->options([
                                'pending' => __('filament.options.status.pending'),
                                'resolved' => __('filament.options.status.approved'), // Using approved label as resolved
                            ])
                            ->required()
                            ->default('pending'),
                    ]),
                ]),
        ]);
    }
}
