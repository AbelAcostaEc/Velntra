<div>
    <x-slot name="header"><x-page-header :title="__t('inventory_report', 'reports')" :description="__t('inventory_description', 'reports')"><x-slot:actions><button type="button" wire:click="exportExcel" wire:loading.class="pointer-events-none opacity-50" wire:target="exportExcel" class="inline-flex h-10 items-center rounded-xl border border-emerald-300 bg-white px-4 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">{{ __t('export_excel', 'reports') }}</button><button type="button" wire:click="exportPdf" wire:loading.class="pointer-events-none opacity-50" wire:target="exportPdf" class="inline-flex h-10 items-center rounded-xl border border-red-300 bg-white px-4 text-sm font-semibold text-red-700 hover:bg-red-50">{{ __t('export_pdf', 'reports') }}</button></x-slot:actions></x-page-header></x-slot>
    <div class="space-y-6">
        <x-card>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_14rem_auto] lg:items-end">
                <div><label for="inventory-search" class="mb-1 block text-sm font-medium text-primary-700">{{ __t('search', 'reports') }}</label><input id="inventory-search" type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __t('search_products', 'reports') }}" class="w-full rounded-xl border-primary-300 text-sm focus:border-accent-500 focus:ring-accent-500"></div>
                <div><label for="inventory-status" class="mb-1 block text-sm font-medium text-primary-700">{{ __t('status', 'reports') }}</label><select id="inventory-status" wire:model.live="status" class="w-full rounded-xl border-primary-300 text-sm focus:border-accent-500 focus:ring-accent-500"><option value="all">{{ __t('all_products', 'reports') }}</option><option value="active">{{ __t('active', 'reports') }}</option><option value="inactive">{{ __t('inactive', 'reports') }}</option><option value="low_stock">{{ __t('low_stock', 'reports') }}</option></select></div>
                <x-per-page-select wire:model.live="perPage" />
            </div>
        </x-card>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <x-stat-card :label="__t('products', 'reports')" :value="number_format($summary['products'])" variant="neutral" />
            <x-stat-card :label="__t('units', 'reports')" :value="number_format($summary['units'])" variant="info" />
            <x-stat-card :label="__t('cost_value', 'reports')" :value="$currency['symbol'].number_format($summary['cost_value'], 2)" :trend="$currency['code']" variant="success" />
            <x-stat-card :label="__t('retail_value', 'reports')" :value="$currency['symbol'].number_format($summary['retail_value'], 2)" :trend="$currency['code']" variant="success" />
            <x-stat-card :label="__t('low_stock', 'reports')" :value="number_format($summary['low_stock'])" variant="warning" />
        </div>
        <x-card :title="__t('inventory_detail', 'reports')">
            @if ($products->isEmpty())<x-empty-state :title="__t('no_results', 'reports')" :description="__t('no_products', 'reports')" />@else
                <div class="-mx-5 overflow-x-auto"><table class="min-w-full divide-y divide-primary-200 text-sm"><thead class="bg-primary-50 text-left text-xs font-semibold uppercase text-primary-500"><tr><th class="px-5 py-3">{{ __t('product', 'reports') }}</th><th class="px-5 py-3">{{ __t('categories', 'reports') }}</th><th class="px-5 py-3 text-right">{{ __t('stock', 'reports') }}</th><th class="px-5 py-3 text-right">{{ __t('unit_cost', 'reports') }}</th><th class="px-5 py-3 text-right">{{ __t('inventory_value', 'reports') }}</th><th class="px-5 py-3">{{ __t('status', 'reports') }}</th></tr></thead><tbody class="divide-y divide-primary-100">@foreach ($products as $product)<tr><td class="px-5 py-3"><p class="font-semibold text-primary-900">{{ $product->name }}</p><p class="text-xs text-primary-500">{{ $product->sku }}</p></td><td class="px-5 py-3 text-primary-600">{{ $product->categories->pluck('name')->join(', ') ?: '—' }}</td><td class="px-5 py-3 text-right"><x-badge :variant="$product->stock <= $product->minimum_stock ? 'warning' : 'success'">{{ $product->stock }}</x-badge></td><td class="px-5 py-3 text-right">{{ $currency['symbol'] }}{{ number_format((float) $product->cost, 2) }}</td><td class="px-5 py-3 text-right font-semibold">{{ $currency['symbol'] }}{{ number_format($product->stock * (float) $product->cost, 2) }}</td><td class="px-5 py-3"><x-badge :variant="$product->is_active ? 'success' : 'neutral'">{{ __t($product->is_active ? 'active' : 'inactive', 'reports') }}</x-badge></td></tr>@endforeach</tbody></table></div>
                <div class="mt-4">{{ $products->links() }}</div>
            @endif
        </x-card>
    </div>
</div>
