<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\MemberPlan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PlanDistributionChart extends ChartWidget
{
    protected ?string $heading = null;

    public function getHeading(): string
    {
        // The original heading was already using a translation.
        // The instruction implies a change, but the provided snippet is malformed.
        // Assuming the intent was to ensure it uses a translation, which it already does.
        return __('filament.widgets.plan_distribution');
    }

    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;
    protected ?string $maxHeight = '200px';



    protected function getData(): array
    {
        $distribution = Subscription::where('status', 'active')
            ->select('plan_id', DB::raw('count(*) as count'))
            ->groupBy('plan_id')
            ->get();
            
        $labels = [];
        $data = [];
        $colors = [
            'rgb(0, 161, 226)',
            'rgb(148, 119, 44)',
            'rgb(204, 84, 144)',
            'rgb(33, 37, 41)',
            'rgb(108, 117, 125)',
        ];

        foreach ($distribution as $item) {
            $plan = MemberPlan::find($item->plan_id);
            $labels[] = $plan ? $plan->name : "Plan #{$item->plan_id}";
            $data[] = $item->count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Subscriptions',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
