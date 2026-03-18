<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class CompanionsRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $relatedResource = UserResource::class;
  
    public static function getRecordLabel(): ?string
    {
        return __('filament.user.companion_label');
    }
    protected static function getModelLabel(): ?string
    {
        return __('filament.user.companion_label');
    }
    protected static function getPluralRecordLabel(): ?string
    {
        return __('filament.user.companion_plural_label');
    }

    protected static function getPluralModelLabel(): ?string
    {
        return __('filament.user.companion_plural_label');
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
