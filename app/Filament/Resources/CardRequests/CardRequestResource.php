<?php

namespace App\Filament\Resources\CardRequests;

use App\Filament\Resources\CardRequests\Pages\ManageCardRequests;
use App\Filament\Resources\CardRequests\Schemas\CardRequestForm;
use App\Filament\Resources\CardRequests\Tables\CardRequestsTable;
use App\Models\CardRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CardRequestResource extends Resource
{
    protected static ?string $model = CardRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.group.users');
    }

    public static function getModelLabel(): string
    {
        return __('filament.card_request.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.card_request.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return CardRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CardRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCardRequests::route('/'),
        ];
    }
}
