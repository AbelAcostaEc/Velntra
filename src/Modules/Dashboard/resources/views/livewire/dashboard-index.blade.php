<div>
    <x-slot name="header">
        <x-page-header
            :title="__t('title', 'dashboard')"
            :description="__t('description', 'dashboard')"
        >
            <x-slot:actions>
                @can('sales.create')
                    <a href="{{ route('sales.index') }}" wire:navigate class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-primary-900 bg-primary-900 px-4 text-sm font-semibold text-white transition hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                        {{ __t('new_sale', 'dashboard') }}
                    </a>
                @endcan
            </x-slot:actions>
        </x-page-header>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card :label="__t('today_sales', 'dashboard')" :value="$currency_symbol.number_format($metrics['today_sales'], 2)" :trend="__t('completed_sales_only', 'dashboard')" variant="success">
                <x-slot:icon><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20m5-16.5H9.5a3 3 0 0 0 0 6H14a3 3 0 0 1 0 6H6"/></svg></x-slot:icon>
            </x-stat-card>

            <x-stat-card :label="__t('monthly_sales', 'dashboard')" :value="$currency_symbol.number_format($metrics['monthly_sales'], 2)" :trend="$currency_code" variant="info">
                <x-slot:icon><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V9m5 10V5m5 14v-7m5 7V3"/></svg></x-slot:icon>
            </x-stat-card>

            <x-stat-card :label="__t('active_products', 'dashboard')" :value="number_format($metrics['total_products'])" :trend="__t('available_catalog', 'dashboard')" variant="neutral">
                <x-slot:icon><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m4 7.5 8-4.5 8 4.5-8 4.5-8-4.5Zm0 0v9l8 4.5 8-4.5v-9M12 12v9"/></svg></x-slot:icon>
            </x-stat-card>

            <x-stat-card :label="__t('active_customers', 'dashboard')" :value="number_format($metrics['total_customers'])" :trend="__t('registered_customers', 'dashboard')" variant="success">
                <x-slot:icon><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19a4 4 0 0 0-8 0M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6.5 5.5c.9-.7 1.5-1.8 1.5-3a3.5 3.5 0 0 0-5.2-3.1"/></svg></x-slot:icon>
            </x-stat-card>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <x-card class="xl:col-span-2">
                <x-slot:header>
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-primary-950">{{ __t('sales_chart', 'dashboard') }}</h3>
                            <p class="mt-1 text-sm text-primary-500">
                                {{ $chartStart->translatedFormat('d M Y') }} – {{ $chartEnd->translatedFormat('d M Y') }}
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <label for="chart-period" class="sr-only">{{ __t('chart_period', 'dashboard') }}</label>
                            <select id="chart-period" wire:model.live="chartPeriod" class="h-9 rounded-lg border-primary-300 bg-white py-1 pl-3 pr-8 text-sm text-primary-700 focus:border-accent-500 focus:ring-accent-500">
                                <option value="7_days">{{ __t('period_seven_days', 'dashboard') }}</option>
                                <option value="current_month">{{ __t('period_current_month', 'dashboard') }}</option>
                                <option value="previous_month">{{ __t('period_previous_month', 'dashboard') }}</option>
                                <option value="custom">{{ __t('period_custom', 'dashboard') }}</option>
                            </select>
                            <x-badge variant="info">{{ $currency_code }}</x-badge>
                        </div>
                    </div>

                    @if ($chartPeriod === 'custom')
                        <form wire:submit="applyCustomPeriod" class="mt-4 grid gap-3 border-t border-primary-100 pt-4 sm:grid-cols-[1fr_1fr_auto] sm:items-start">
                            <div>
                                <label for="date-from" class="mb-1 block text-xs font-medium text-primary-600">{{ __t('date_from', 'dashboard') }}</label>
                                <input id="date-from" type="date" wire:model="dateFrom" class="h-9 w-full rounded-lg border-primary-300 text-sm focus:border-accent-500 focus:ring-accent-500">
                                @error('dateFrom') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="date-to" class="mb-1 block text-xs font-medium text-primary-600">{{ __t('date_to', 'dashboard') }}</label>
                                <input id="date-to" type="date" wire:model="dateTo" class="h-9 w-full rounded-lg border-primary-300 text-sm focus:border-accent-500 focus:ring-accent-500">
                                @error('dateTo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <button type="submit" class="mt-5 inline-flex h-9 items-center justify-center rounded-lg bg-primary-900 px-4 text-sm font-semibold text-white transition hover:bg-primary-800">
                                {{ __t('apply_period', 'dashboard') }}
                            </button>
                        </form>
                    @endif
                </x-slot:header>

                <div class="overflow-x-auto pb-2">
                    <div class="flex h-64 items-end gap-2" style="min-width: max(100%, {{ count($chart) * 42 }}px)" role="img" aria-label="{{ __t('sales_chart', 'dashboard') }}">
                        @foreach ($chart as $day)
                            @php($height = $day['total'] > 0 ? max(8, ($day['total'] / $chartMaximum) * 100) : 2)
                            <div class="group flex h-full min-w-8 flex-1 flex-col justify-end gap-2">
                                <div class="text-center text-xs font-medium text-primary-600 opacity-0 transition group-hover:opacity-100">
                                    {{ $currency_symbol }}{{ number_format($day['total'], 2) }}
                                </div>
                                <div class="relative flex h-48 items-end overflow-hidden rounded-lg bg-primary-100">
                                    <div class="w-full rounded-lg bg-primary-800 transition-all duration-300 group-hover:bg-accent-600" style="height: {{ $height }}%"></div>
                                </div>
                                <div class="text-center">
                                    <p class="text-[10px] font-semibold uppercase text-primary-700">{{ $day['label'] }}</p>
                                    @if (count($chart) <= 14)
                                        <p class="text-[10px] text-primary-400">{{ \Carbon\CarbonImmutable::parse($day['date'])->format('d/m') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-card>

            <x-card :title="__t('quick_actions', 'dashboard')" :description="__t('quick_actions_description', 'dashboard')">
                <div class="space-y-3">
                    @can('sales.create')
                        <a href="{{ route('sales.index') }}" wire:navigate class="flex items-center justify-between rounded-xl border border-primary-200 p-3 text-sm font-medium text-primary-700 transition hover:border-primary-300 hover:bg-primary-50 hover:text-primary-950">
                            <span>{{ __t('new_sale', 'dashboard') }}</span><span aria-hidden="true">→</span>
                        </a>
                    @endcan
                    @can('products.create')
                        <a href="{{ route('products.index') }}" wire:navigate class="flex items-center justify-between rounded-xl border border-primary-200 p-3 text-sm font-medium text-primary-700 transition hover:border-primary-300 hover:bg-primary-50 hover:text-primary-950">
                            <span>{{ __t('new_product', 'dashboard') }}</span><span aria-hidden="true">→</span>
                        </a>
                    @endcan
                    @can('customers.create')
                        <a href="{{ route('customers.index') }}" wire:navigate class="flex items-center justify-between rounded-xl border border-primary-200 p-3 text-sm font-medium text-primary-700 transition hover:border-primary-300 hover:bg-primary-50 hover:text-primary-950">
                            <span>{{ __t('new_customer', 'dashboard') }}</span><span aria-hidden="true">→</span>
                        </a>
                    @endcan
                </div>
            </x-card>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <x-card :title="__t('latest_sales', 'dashboard')" :description="__t('latest_sales_description', 'dashboard')">
                @if ($latestSales->isEmpty())
                    <x-empty-state :title="__t('no_sales', 'dashboard')" :description="__t('no_sales_description', 'dashboard')" />
                @else
                    <div class="-mx-5 overflow-x-auto">
                        <table class="min-w-full divide-y divide-primary-200 text-sm">
                            <thead class="bg-primary-50 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">
                                <tr><th class="px-5 py-3">{{ __t('sale_number', 'dashboard') }}</th><th class="px-5 py-3">{{ __t('customer', 'dashboard') }}</th><th class="px-5 py-3 text-right">{{ __t('total', 'dashboard') }}</th></tr>
                            </thead>
                            <tbody class="divide-y divide-primary-100">
                                @foreach ($latestSales as $sale)
                                    <tr>
                                        <td class="px-5 py-3"><p class="font-semibold text-primary-900">{{ $sale->number }}</p><p class="text-xs text-primary-500">{{ $sale->created_at->diffForHumans() }}</p></td>
                                        <td class="px-5 py-3 text-primary-700">{{ $sale->customer?->name ?? '—' }}</td>
                                        <td class="px-5 py-3 text-right font-semibold text-primary-950">{{ $currency_symbol }}{{ number_format((float) $sale->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>

            <x-card :title="__t('low_stock', 'dashboard')" :description="__t('low_stock_description', 'dashboard')">
                @if ($lowStockProducts->isEmpty())
                    <x-empty-state :title="__t('stock_healthy', 'dashboard')" :description="__t('stock_healthy_description', 'dashboard')" />
                @else
                    <div class="space-y-3">
                        @foreach ($lowStockProducts as $product)
                            <div class="flex items-center justify-between gap-4 rounded-xl border border-primary-200 p-3">
                                <div class="min-w-0"><p class="truncate text-sm font-semibold text-primary-900">{{ $product->name }}</p><p class="mt-1 text-xs text-primary-500">{{ $product->sku }} · {{ $product->categories->pluck('name')->join(', ') ?: __t('uncategorized', 'dashboard') }}</p></div>
                                <div class="shrink-0 text-right"><x-badge :variant="$product->stock <= 0 ? 'danger' : 'warning'">{{ $product->stock }} / {{ $product->minimum_stock }}</x-badge></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</div>
