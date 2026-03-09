<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make(__('filament.sections.general'))
                        ->schema([
                            TextInput::make('name')
                                ->label(__('filament.fields.name'))
                                ->required(),
                            TextInput::make('email')
                                ->label(__('filament.fields.email'))
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true),
                            TextInput::make('phone')
                                ->label(__('filament.fields.phone'))
                                ->tel(),
                        ])->columns(2),

                    Section::make(__('filament.fields.password'))
                        ->schema([
                            TextInput::make('password')
                                ->label(__('filament.fields.password'))
                                ->password()
                                ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                ->dehydrated(fn($state) => filled($state))
                                ->required(fn(string $context): bool => $context === 'create'),
                        ]),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.media'))
                        ->schema([
                            FileUpload::make('avatar')
                                ->label(__('filament.fields.image'))
                                ->image()
                                ->directory('users/avatars'),
                        ]),

                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            Select::make('role')
                                ->label(__('filament.fields.role'))
                                ->options(__('filament.options.role'))
                                ->required(),
                            Toggle::make('is_active')
                                ->label(__('filament.fields.is_active'))
                                ->default(true),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
