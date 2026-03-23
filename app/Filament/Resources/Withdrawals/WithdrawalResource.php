<?php

namespace App\Filament\Resources\Withdrawals;

use App\Filament\Resources\Withdrawals\Pages\ManageWithdrawals;
use App\Filament\Resources\Withdrawals\Schemas\WithdrawalForm;
use App\Filament\Resources\Withdrawals\Tables\WithdrawalsTable;
use App\Models\Withdrawal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Infolists\Infolist;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\Withdrawals\Schemas\WithdrawalInfolist;


class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    
    protected static ?int $navigationSort = 11;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.group.wallet');
    }

    public static function getModelLabel(): string
    {
        return __('filament.withdrawal.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.withdrawal.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return WithdrawalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WithdrawalsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WithdrawalInfolist::configure($schema);
    }




    public static function getPages(): array
    {
        return [
            'index' => ManageWithdrawals::route('/'),
        ];
    }
}
