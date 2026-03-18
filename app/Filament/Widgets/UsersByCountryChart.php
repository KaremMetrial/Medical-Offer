<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Country;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UsersByCountryChart extends ChartWidget
{
    protected ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.widgets.users_by_country');
    }

    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 1;
    protected ?string $maxHeight = '200px';


    protected function getData(): array
    {
        $distribution = User::select('country_id', DB::raw('count(*) as count'))
            ->groupBy('country_id')
            ->get();
            
        $labels = [];
        $data = [];
        $colors = [
            'rgb(54, 162, 235)',
            'rgb(255, 99, 132)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(153, 102, 255)',
            'rgb(255, 159, 64)',
        ];

        foreach ($distribution as $item) {
            $country = Country::find($item->country_id);
            $labels[] = $country ? $country->name : 'Unknown';
            $data[] = $item->count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
