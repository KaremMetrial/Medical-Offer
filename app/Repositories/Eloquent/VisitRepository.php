<?php

namespace App\Repositories\Eloquent;

use App\Models\Visit;
use App\Repositories\Contracts\VisitRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class VisitRepository extends BaseRepository implements VisitRepositoryInterface
{
    /**
     * VisitRepository constructor.
     *
     * @param Visit $model
     */
    public function __construct(Visit $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritdoc
     */
    public function countByProviderId(int $providerId): int
    {
        return $this->model->where('provider_id', $providerId)->count();
    }

    /**
     * @inheritdoc
     */
    public function sumPaidAmountByProviderId(int $providerId): float
    {
        return (float) $this->model->where('provider_id', $providerId)->sum('paid_amount');
    }

    /**
     * @inheritdoc
     */
    public function getVisitMovement(int $providerId, string $filter): array
    {
        if ($filter === 'month') {
            return $this->getMonthlyMovement($providerId);
        }

        return $this->getDailyMovement($providerId);
    }

    /**
     * @inheritdoc
     */
    private function getDailyMovement(int $providerId): array
    {
        $days = [];
        $dayNames = [
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
        ];

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i);
            $count = $this->model->where('provider_id', $providerId)
                ->whereDate('visit_date', $date->toDateString())
                ->count();

            $days[] = [
                'label' => $dayNames[$date->format('l')] ?? $date->format('l'),
                'count' => $count,
                'date' => $date->format('Y-m-d'),
            ];
        }

        return array_reverse($days);
    }

    /**
     * @inheritdoc
     */
    private function getMonthlyMovement(int $providerId): array
    {
        $months = [];
        $monthNames = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::now()->subMonths($i);
            $count = $this->model->where('provider_id', $providerId)
                ->whereYear('visit_date', $date->year)
                ->whereMonth('visit_date', $date->month)
                ->count();

            $months[] = [
                'label' => $monthNames[$date->month] ?? $date->format('M'),
                'count' => $count,
                'period' => $date->format('Y-m'),
            ];
        }

        return array_reverse($months);
    }

    /**
     * @inheritdoc
     */
    public function getPaginatedForProvider(int $providerId, int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->with([
            'user.governorate.translations',
            'user.city.translations',
            'companion.governorate.translations',
            'companion.city.translations'
        ])
        ->where('provider_id', $providerId)
        ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($qu) use ($search) {
                    $qu->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('companion', function ($qc) use ($search) {
                    $qc->where('name', 'like', "%{$search}%");
                })
                ->orWhere('services', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * @inheritdoc
     */
    public function store(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function getDetails(int $id, int $providerId)
    {
        return $this->model->with([
            'user.governorate.translations',
            'user.city.translations',
            'companion.governorate.translations',
            'companion.city.translations',
            'user.subscriptions.plan.translations',
            'companion.subscriptions.plan.translations',
            'user.country',
            'companion.country'
        ])
        ->where('provider_id', $providerId)
        ->findOrFail($id);
    }
}
