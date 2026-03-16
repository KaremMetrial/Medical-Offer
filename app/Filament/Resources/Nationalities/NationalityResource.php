<?php

namespace App\Filament\Resources\Nationalities;

use App\Filament\Resources\Nationalities\Pages\CreateNationality;
use App\Filament\Resources\Nationalities\Pages\EditNationality;
use App\Filament\Resources\Nationalities\Pages\ListNationalities;
use App\Filament\Resources\Nationalities\Schemas\NationalityForm;
use App\Filament\Resources\Nationalities\Tables\NationalitiesTable;
use App\Models\Nationality;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NationalityResource extends Resource
{
    protected static ?string $model = Nationality::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::GlobeAlt;
    protected static ?string $recordTitleAttribute = 'name';
    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.group.locations');
    }
    public static function getModelLabel(): string
    {
        return __('filament.nationality.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.nationality.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return NationalityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NationalitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNationalities::route('/'),
            'create' => CreateNationality::route('/create'),
            'edit' => EditNationality::route('/{record}/edit'),
        ];
    }
}
