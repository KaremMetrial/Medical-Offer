<?php

namespace App\Filament\Resources\WalletTransactions\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class UserRelationManager extends RelationManager
{
    protected static string $relationship = 'user';

    protected static ?string $relatedResource = UserResource::class;

    public static function getRecordLabel(): ?string
    {
        return __('filament.user.label');
    }

    protected static function getModelLabel(): ?string
    {
        return __('filament.user.label');
    }

    protected static function getPluralRecordLabel(): ?string
    {
        return __('filament.user.plural_label');
    }

    protected static function getPluralModelLabel(): ?string
    {
        return __('filament.user.plural_label');
    }

    public function table(Table $table): Table
    {
        return $table;
    }
}
