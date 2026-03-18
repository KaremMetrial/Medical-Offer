<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';
    public static function getRecordLabel(): ?string
    {
        return __('filament.attachments.label');
    }

    protected static function getPluralRecordLabel(): ?string
    {
        return __('filament.attachments.plural_label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\FileUpload::make('path')
                    ->label(__('filament.fields.file'))
                    ->disk('public')
                    ->directory('users/attachments')
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('file_type', pathinfo($state, PATHINFO_EXTENSION));
                        }
                    }),
                \Filament\Forms\Components\Hidden::make('file_type'),
                \Filament\Forms\Components\Select::make('type')
                    ->label(__('filament.fields.type'))
                    ->options([
                        'id_card' => __('filament.options.attachment_type.id_card'),
                        'passport' => __('filament.options.attachment_type.passport'),
                        'photo' => __('filament.options.attachment_type.photo'),
                        'other' => __('filament.options.attachment_type.other'),
                    ])
                    ->required(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('attachable_type'),
                TextEntry::make('attachable_id')
                    ->numeric(),
                TextEntry::make('path'),
                TextEntry::make('type')
                    ->placeholder('-'),
                TextEntry::make('file_type')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('path')
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('path')
                    ->label(__('filament.fields.file'))
                    ->disk('public'),
                TextColumn::make('type')
                    ->label(__('filament.fields.type'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('file_type')
                    ->label(__('filament.fields.file_type'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
