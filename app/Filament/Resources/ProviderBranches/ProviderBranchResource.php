<?php

namespace App\Filament\Resources\ProviderBranches;

use App\Filament\Resources\ProviderBranches\Pages\CreateProviderBranch;
use App\Filament\Resources\ProviderBranches\Pages\EditProviderBranch;
use App\Filament\Resources\ProviderBranches\Pages\ListProviderBranches;
use App\Filament\Resources\ProviderBranches\Schemas\ProviderBranchForm;
use App\Filament\Resources\ProviderBranches\Tables\ProviderBranchesTable;
use App\Models\ProviderBranch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProviderBranchResource extends Resource
{
    protected static ?string $model = ProviderBranch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static ?string $recordTitleAttribute = 'name_en';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.group.business');
    }

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('filament.provider_branch.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.provider_branch.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return ProviderBranchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProviderBranchesTable::configure($table);
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
            'index' => ListProviderBranches::route('/'),
            'create' => CreateProviderBranch::route('/create'),
            'edit' => EditProviderBranch::route('/{record}/edit'),
        ];
    }
}
