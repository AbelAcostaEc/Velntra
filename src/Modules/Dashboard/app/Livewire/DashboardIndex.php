<?php

namespace Modules\Dashboard\Livewire;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Modules\Dashboard\Services\DashboardService;

class DashboardIndex extends Component
{
    public string $chartPeriod = '7_days';

    public string $dateFrom = '';

    public string $dateTo = '';

    public function mount(): void
    {
        Gate::authorize('dashboard.view');

        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function updatedChartPeriod(): void
    {
        if (! in_array($this->chartPeriod, ['7_days', 'current_month', 'previous_month', 'custom'], true)) {
            $this->chartPeriod = '7_days';
        }

        $this->resetValidation();
    }

    public function applyCustomPeriod(): void
    {
        $validated = $this->validate([
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date', 'after_or_equal:dateFrom'],
        ]);

        $start = CarbonImmutable::parse($validated['dateFrom']);
        $end = CarbonImmutable::parse($validated['dateTo']);

        if ($start->diffInDays($end) > 92) {
            $this->addError('dateTo', __t('range_too_large', 'dashboard'));

            return;
        }

        $this->dateFrom = $start->toDateString();
        $this->dateTo = $end->toDateString();
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function chartDates(): array
    {
        $today = CarbonImmutable::today();

        return match ($this->chartPeriod) {
            'current_month' => [$today->startOfMonth(), $today],
            'previous_month' => [$today->subMonthNoOverflow()->startOfMonth(), $today->subMonthNoOverflow()->endOfMonth()],
            'custom' => [
                CarbonImmutable::parse($this->dateFrom),
                CarbonImmutable::parse($this->dateTo),
            ],
            default => [$today->subDays(6), $today],
        };
    }

    public function render(DashboardService $dashboardService): View
    {
        Gate::authorize('dashboard.view');

        [$chartStart, $chartEnd] = $this->chartDates();
        $chart = $dashboardService->salesChart($chartStart, $chartEnd);
        $chartMaximum = max(array_column($chart, 'total'));

        return view('dashboard::livewire.dashboard-index', [
            'metrics' => $dashboardService->metrics(),
            'latestSales' => $dashboardService->latestSales(),
            'lowStockProducts' => $dashboardService->lowStockProducts(),
            'chart' => $chart,
            'chartMaximum' => max($chartMaximum, 1),
            'chartStart' => $chartStart,
            'chartEnd' => $chartEnd,
            ...$dashboardService->presentation(),
        ]);
    }
}
