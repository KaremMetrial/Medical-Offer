<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyRevenueChart extends ChartWidget
{
    protected ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.widgets.monthly_revenue');
    }

    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected ?string $maxHeight = '200px';


    protected function getData(): array
    {
        // Simple manual trend calculation to avoid external dependencies if Flowframe Trend is not installed
        // We can use a query with groupBy or a simple loop for the last 12 months
        
        $data = [];
        $labels = [];
        
        $months = 12;
        $now = Carbon::now();
        $currencyService = app(\App\Services\CurrencyService::class);
        $systemBase = config('settings.currency.system_base', 'USD');
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = $now->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $now->copy()->subMonths($i)->endOfMonth();
            
            $revenueInUSD = Payment::where('status', 'paid')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $revenueInEGP = $currencyService->convert((float)$revenueInUSD, $systemBase, 'EGP');
            
            $labels[] = $monthStart->format('M Y');
            $data[] = round($revenueInEGP, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(0, 161, 226, 0.1)',
                    'borderColor' => 'rgb(0, 161, 226)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
