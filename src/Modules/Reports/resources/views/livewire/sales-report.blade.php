<div>
    <x-slot name="header">
        <x-page-header :title="__t('sales_report', 'reports')" :description="__t('sales_description', 'reports')" />
    </x-slot>

    <div class="space-y-6">
        <x-card>
            <form wire:submit="applyFilters" class="grid gap-4 sm:grid-cols-[1fr_1fr_auto] sm:items-start">
                <div><label for="sales-from" class="mb-1 block text-sm font-medium text-primary-700">{{ __t('date_from', 'reports') }}</label><input id="sales-from" type="date" wire:model="dateFrom" class="w-full rounded-xl border-primary-300 text-sm focus:border-accent-500 focus:ring-accent-500">@error('dateFrom')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="sales-to" class="mb-1 block text-sm font-medium text-primary-700">{{ __t('date_to', 'reports') }}</label><input id="sales-to" type="date" wire:model="dateTo" class="w-full rounded-xl border-primary-300 text-sm focus:border-accent-500 focus:ring-accent-500">@error('dateTo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                <button type="submit" class="mt-6 h-10 rounded-xl bg-primary-900 px-5 text-sm font-semibold text-white hover:bg-primary-800">{{ __t('apply_filters', 'reports') }}</button>
            </form>
        </x-card>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card :label="__t('total_sold', 'reports')" :value="$currency['symbol'].number_format($summary['total'], 2)" :trend="$currency['code']" variant="success" />
            <x-stat-card :label="__t('total_tax', 'reports')" :value="$currency['symbol'].number_format($summary['tax'], 2)" :trend="__t('completed_only', 'reports')" variant="info" />
            <x-stat-card :label="__t('sales_count', 'reports')" :value="number_format($summary['count'])" :trend="__t('completed_only', 'reports')" variant="neutral" />
            <x-stat-card :label="__t('average_ticket', 'reports')" :value="$currency['symbol'].number_format($summary['average'], 2)" :trend="$currency['code']" variant="success" />
        </div>

        <x-card :title="__t('sales_detail', 'reports')">
            <div class="mb-4 flex justify-end"><x-per-page-select wire:model.live="perPage" /></div>
            @if ($sales->isEmpty())
                <x-empty-state :title="__t('no_results', 'reports')" :description="__t('no_sales_period', 'reports')" />
            @else
                <div class="-mx-5 overflow-x-auto"><table class="min-w-full divide-y divide-primary-200 text-sm"><thead class="bg-primary-50 text-left text-xs font-semibold uppercase text-primary-500"><tr><th class="px-5 py-3">{{ __t('sale', 'reports') }}</th><th class="px-5 py-3">{{ __t('date', 'reports') }}</th><th class="px-5 py-3">{{ __t('customer', 'reports') }}</th><th class="px-5 py-3">{{ __t('payment', 'reports') }}</th><th class="px-5 py-3 text-right">{{ __t('tax', 'reports') }}</th><th class="px-5 py-3 text-right">{{ __t('total', 'reports') }}</th></tr></thead><tbody class="divide-y divide-primary-100">@foreach ($sales as $sale)<tr><td class="px-5 py-3 font-semibold text-primary-900">{{ $sale->number }}</td><td class="whitespace-nowrap px-5 py-3 text-primary-600">{{ $sale->created_at->format('d/m/Y H:i') }}</td><td class="px-5 py-3 text-primary-700">{{ $sale->customer?->name ?? '—' }}</td><td class="px-5 py-3"><x-badge>{{ __t('payment_'.$sale->payment_method, 'reports') }}</x-badge></td><td class="px-5 py-3 text-right">{{ $currency['symbol'] }}{{ number_format((float) $sale->tax, 2) }}</td><td class="px-5 py-3 text-right font-semibold">{{ $currency['symbol'] }}{{ number_format((float) $sale->total, 2) }}</td></tr>@endforeach</tbody></table></div>
                <div class="mt-4">{{ $sales->links() }}</div>
            @endif
        </x-card>
    </div>
</div>
