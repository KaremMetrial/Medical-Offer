<?php

namespace App\Filament\Pages\ContactSettings\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactSettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('countries')
                    ->label(__('filament.fields.countries'))
                    ->schema([
                        Section::make(__('filament.fields.contact_title'))
                            ->schema(function () {
                                $fields = [];
                                foreach (config('languages.supported', ['ar' => 'Arabic', 'en' => 'English']) as $code => $name) {
                                    $fields[] = TextInput::make("contact_title:{$code}")
                                        ->required(fn() => $code === app()->getLocale())
                                        ->label(__('filament.fields.contact_title') . " ({$name})");
                                }
                                return $fields;
                            })->columns(2),
                        Section::make(fn ($state) => $state['name'] ?? 'Country')
                            ->schema([
                                TextInput::make('name')
                                    ->disabled()
                                    ->required()
                                    ->dehydrated(false),
                                TextInput::make('contact_email')
                                    ->label(__('filament.fields.contact_email'))
                                    ->required()
                                    ->email(),
                                TextInput::make('contact_phone')
                                    ->required()
                                    ->label(__('filament.fields.contact_phone')),
                                TextInput::make('contact_whatsapp')
                                    ->required()
                                    ->label(__('filament.fields.contact_whatsapp')),
                                    
                           
                            ])->columns(2),
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
            ]);
    }
}
