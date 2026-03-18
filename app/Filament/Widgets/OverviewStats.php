<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $currencyService = app(\App\Services\CurrencyService::class);
        $revenueInEGP = $currencyService->convert((float)$totalRevenue, config('settings.currency.system_base', 'USD'), 'EGP');

        return [
            Stat::make(__('filament.widgets.total_users'), number_format($totalUsers))
                ->description(__('filament.widgets.total_users_desc'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make(__('filament.widgets.total_revenue'), number_format($revenueInEGP, 2) . ' EGP')
                ->description(__('filament.widgets.total_revenue_desc'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make(__('filament.widgets.active_subscriptions'), number_format($activeSubscriptions))
                ->description(__('filament.widgets.active_subscriptions_desc'))
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),
        ];
    }
}
