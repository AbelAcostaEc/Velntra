<?php

namespace Modules\Reports\Services;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Modules\Customers\Models\Customer;
use Modules\Inventory\Models\Product;
use Modules\Sales\Models\Sale;
use Modules\Settings\Models\Setting;

class ReportService
{
    /** @return array{symbol: string, code: string} */
    public function currency(): array
    {
        $currency = Setting::getSettings()->currency;

        return [
            'symbol' => $currency?->symbol ?? '$',
            'code' => $currency?->code ?? 'USD',
        ];
    }

    /**
     * @return Builder<Sale>
     */
    public function salesQuery(string $dateFrom, string $dateTo): Builder
    {
        $query = Sale::query()
            ->completed()
            ->with(['customer', 'user']);

        return $this->applyDateRange($query, $dateFrom, $dateTo)
            ->latest('created_at')
            ->latest('id');
    }

    /**
     * @return array{total: float, tax: float, count: int, average: float}
     */
    public function salesSummary(string $dateFrom, string $dateTo): array
    {
        $query = $this->applyDateRange(Sale::query()->completed(), $dateFrom, $dateTo);
        $count = (clone $query)->count();
        $total = (float) (clone $query)->sum('total');

        return [
            'total' => $total,
            'tax' => (float) (clone $query)->sum('tax'),
            'count' => $count,
            'average' => $count > 0 ? $total / $count : 0,
        ];
    }

    /**
     * @return Builder<Product>
     */
    public function inventoryQuery(string $search = '', string $status = 'all'): Builder
    {
        return Product::query()
            ->with('categories')
            ->search($search)
            ->when($status === 'active', fn (Builder $query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn (Builder $query) => $query->where('is_active', false))
            ->when($status === 'low_stock', fn (Builder $query) => $query->lowStock())
            ->orderBy('name');
    }

    /**
     * @return array{products: int, units: int, cost_value: float, retail_value: float, low_stock: int}
     */
    public function inventorySummary(string $search = '', string $status = 'all'): array
    {
        $query = $this->inventoryQuery($search, $status);

        return [
            'products' => (clone $query)->count(),
            'units' => (int) (clone $query)->sum('stock'),
            'cost_value' => (float) (clone $query)->selectRaw('COALESCE(SUM(stock * cost), 0) as value')->value('value'),
            'retail_value' => (float) (clone $query)->selectRaw('COALESCE(SUM(stock * price), 0) as value')->value('value'),
            'low_stock' => (clone $query)->lowStock()->count(),
        ];
    }

    /**
     * @return Builder<Customer>
     */
    public function customersQuery(string $dateFrom, string $dateTo, string $search = ''): Builder
    {
        $bounds = $this->dateBounds($dateFrom, $dateTo);

        if ($bounds === null) {
            return Customer::query()->whereRaw('1 = 0');
        }

        $completedSales = fn (Builder $query) => $query
            ->completed()
            ->whereBetween('created_at', $bounds);

        return Customer::query()
            ->search($search)
            ->whereHas('sales', $completedSales)
            ->withCount(['sales as purchases_count' => $completedSales])
            ->withSum(['sales as purchases_total' => $completedSales], 'total')
            ->orderByDesc('purchases_total')
            ->orderByDesc('purchases_count')
            ->orderBy('name');
    }

    /**
     * @return array{customers: int, purchases: int, total: float}
     */
    public function customersSummary(string $dateFrom, string $dateTo, string $search = ''): array
    {
        $customers = $this->customersQuery($dateFrom, $dateTo, $search)->get();

        return [
            'customers' => $customers->count(),
            'purchases' => (int) $customers->sum('purchases_count'),
            'total' => (float) $customers->sum('purchases_total'),
        ];
    }

    public function hasValidDateRange(string $dateFrom, string $dateTo): bool
    {
        return $this->dateBounds($dateFrom, $dateTo) !== null;
    }

    /** @param Builder<Sale> $query
     * @return Builder<Sale>
     */
    private function applyDateRange(Builder $query, string $dateFrom, string $dateTo): Builder
    {
        $bounds = $this->dateBounds($dateFrom, $dateTo);

        return $bounds === null
            ? $query->whereRaw('1 = 0')
            : $query->whereBetween('created_at', $bounds);
    }

    /** @return array{0: CarbonImmutable, 1: CarbonImmutable}|null */
    private function dateBounds(string $dateFrom, string $dateTo): ?array
    {
        try {
            $start = CarbonImmutable::createFromFormat('!Y-m-d', $dateFrom);
            $end = CarbonImmutable::createFromFormat('!Y-m-d', $dateTo);
        } catch (\Throwable) {
            return null;
        }

        if ($start === null || $end === null || $start->format('Y-m-d') !== $dateFrom || $end->format('Y-m-d') !== $dateTo || $start->isAfter($end)) {
            return null;
        }

        return [$start->startOfDay(), $end->endOfDay()];
    }
}
