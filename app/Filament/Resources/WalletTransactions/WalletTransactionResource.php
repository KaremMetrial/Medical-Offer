<?php

namespace App\Filament\Resources\WalletTransactions;

use App\Filament\Resources\WalletTransactions\Pages\CreateWalletTransaction;
use App\Filament\Resources\WalletTransactions\Pages\EditWalletTransaction;
use App\Filament\Resources\WalletTransactions\Pages\ListWalletTransactions;
use App\Filament\Resources\WalletTransactions\Schemas\WalletTransactionForm;
use App\Filament\Resources\WalletTransactions\Tables\WalletTransactionsTable;
use App\Models\WalletTransaction;
use App\Filament\Resources\WalletTransactions\RelationManagers;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WalletTransactionResource extends Resource
{
    protected static ?string $model = WalletTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $recordTitleAttribute = 'description';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.group.financial');
    }

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('filament.wallet_transaction.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.wallet_transaction.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return WalletTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WalletTransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UserRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWalletTransactions::route('/'),
            // 'create' => CreateWalletTransaction::route('/create'),
            // 'edit' => EditWalletTransaction::route('/{record}/edit'),
        ];
    }
}