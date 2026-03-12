<?php

namespace App\Filament\Resources\Stories\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use App\Models\Provider;
use App\Traits\UploadTrait;

class StoryForm
{
    use UploadTrait;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make(__('filament.sections.general'))
                        ->schema([
                            Select::make('provider_id')
                                ->label(__('filament.fields.provider'))
                                ->options(fn() => Provider::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            Select::make('countries')
                                ->label(__('filament.fields.countries'))
                                ->multiple()
                                ->relationship('countries', 'id')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name ?? $record->id)
                                ->preload()
                                ->required(),
                        ]),

                    Section::make(__('filament.sections.link_info'))
                        ->schema([
                            TextInput::make('external_link')
                                ->label(__('filament.fields.external_link'))
                                ->url()
                                ->placeholder('https://example.com')
                                ->default(null),
                        ]),
                ])->columnSpan(3),

                Group::make([
                    Section::make(__('filament.sections.media'))
                        ->schema([
                            Select::make('story_type')
                                ->label(__('filament.fields.story_type'))
                                ->options([
                                    'image' => 'Image',
                                    'video' => 'Video',
                                ])
                                ->default('image')
                                ->live()
                                ->required(),

                            FileUpload::make('image_path')
                                ->label(__('filament.fields.media_url') . ' (Image)')
                                ->disk('public')
                                ->directory('stories')
                                ->visibility('public')
                                ->image()
                                ->visible(fn(callable $get) => $get('story_type') === 'image')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(20480),

                            FileUpload::make('video_path')
                                ->label(__('filament.fields.media_url') . ' (Video)')
                                ->disk('public')
                                ->directory('stories')
                                ->visibility('public')
                                ->visible(fn(callable $get) => $get('story_type') === 'video')
                                ->acceptedFileTypes(['video/mp4', 'video/quicktime'])
                                ->maxSize(51200),
                        ]),

                    Section::make(__('filament.sections.settings'))
                        ->schema([
                            DateTimePicker::make('expiry_time')
                                ->label(__('filament.fields.expiry_time'))
                                ->default(now()->addHours(24))
                                ->required(),
                        ]),
                ])->columnSpan(3),
            ]);
    }
}
