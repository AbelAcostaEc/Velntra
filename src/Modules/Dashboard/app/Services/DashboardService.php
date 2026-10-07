<?php

namespace Modules\Dashboard\Services;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Modules\Customers\Models\Customer;
use Modules\Inventory\Models\Product;
use Modules\Sales\Models\Sale;
use Modules\Settings\Models\Setting;

class DashboardService
{
    /**
     * Return the primary business indicators.
     *
     * @return array<string, int|float>
     */
    public function metrics(): array
    {
        $now = CarbonImmutable::now();
        $completedSales = Sale::query()->completed();

        return [
            'today_sales' => (float) (clone $completedSales)
                ->whereBetween('created_at', [$now->startOfDay(), $now->endOfDay()])
                ->sum('total'),
            'monthly_sales' => (float) (clone $completedSales)
                ->whereBetween('created_at', [$now->startOfMonth(), $now->endOfMonth()])
                ->sum('total'),
            'total_products' => Product::query()->active()->count(),
            'total_customers' => Customer::query()->active()->count(),
        ];
    }

    /**
     * Return the most recent completed sales.
     *
     * @return Collection<int, Sale>
     */
    public function latestSales(int $limit = 5): Collection
    {
        return Sale::query()
            ->completed()
            ->with(['customer', 'user'])
            ->latest('created_at')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Return active products whose stock reached the configured minimum.
     *
     * @return Collection<int, Product>
     */
    public function lowStockProducts(int $limit = 5): Collection
    {
        return Product::query()
            ->active()
            ->lowStock()
            ->with('categories')
            ->orderBy('stock')
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Return completed sales totals for each day in the requested period.
     *
     * @return array<int, array{date: string, label: string, total: float}>
     */
    public function salesChart(?CarbonInterface $startDate = null, ?CarbonInterface $endDate = null): array
    {
        $end = CarbonImmutable::instance($endDate ?? CarbonImmutable::today())->endOfDay();
        $start = CarbonImmutable::instance($startDate ?? $end->subDays(6))->startOfDay();
        $days = (int) $start->diffInDays($end);

        $totals = Sale::query()
            ->completed()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as sale_date, SUM(total) as aggregate')
            ->groupBy('sale_date')
            ->pluck('aggregate', 'sale_date');

        return collect(range(0, $days))
            ->map(function (int $offset) use ($days, $start, $totals): array {
                $date = $start->addDays($offset);
                $key = $date->toDateString();

                return [
                    'date' => $key,
                    'label' => $days <= 13
                        ? $date->translatedFormat('D')
                        : $date->format('d/m'),
                    'total' => (float) ($totals[$key] ?? 0),
                ];
            })
            ->all();
    }

    /**
     * Return presentation settings required by the dashboard.
     *
     * @return array{currency_symbol: string, currency_code: string}
     */
    public function presentation(): array
    {
        $settings = Setting::getSettings();

        return [
            'currency_symbol' => $settings->currency?->symbol ?? '$',
            'currency_code' => $settings->currency?->code ?? 'USD',
        ];
    }
}
