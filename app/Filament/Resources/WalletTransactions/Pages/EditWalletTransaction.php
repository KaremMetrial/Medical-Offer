<?php

namespace App\Filament\Resources\WalletTransactions\Pages;

use App\Filament\Resources\WalletTransactions\WalletTransactionResource;
use Filament\Resources\Pages\EditRecord;

class EditWalletTransaction extends EditRecord
{
    protected static string $resource = WalletTransactionResource::class;
}