<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPayments extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('filament.widgets.latest_payments');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('payable.name')
                    ->label(__('filament.fields.name'))
                    ->default(fn($record) => $record->payable?->name ?? $record->payable?->email ?? '-'),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament.fields.amount'))
                    ->formatStateUsing(function ($state) {
                        $currencyService = app(\App\Services\CurrencyService::class);
                        $systemBase = config('settings.currency.system_base', 'USD');
                        $amountInEGP = $currencyService->convert((float)$state, $systemBase, 'EGP');
                        return number_format($amountInEGP, 2) . ' EGP';
                    }),
                Tables\Columns\TextColumn::make('method')
                    ->label(__('filament.fields.method')),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime(),
            ])
            ->paginated(false);
    }
}
