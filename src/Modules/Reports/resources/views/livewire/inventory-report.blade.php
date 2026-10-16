<div>
    <div class="mb-6">
        <x-page-header :title="__t('inventory_report', 'reports')" :description="__t('inventory_description', 'reports')">
            <x-slot:actions>
                <button type="button" wire:click="exportExcel" wire:loading.attr="disabled" wire:target="exportExcel" class="inline-flex h-10 items-center rounded-xl border border-emerald-700 bg-emerald-700 px-4 text-sm font-semibold text-white hover:bg-emerald-800 disabled:cursor-wait disabled:opacity-50">{{ __t('export_excel', 'reports') }}</button>
                <button type="button" wire:click="exportPdf" wire:loading.attr="disabled" wire:target="exportPdf" class="inline-flex h-10 items-center rounded-xl border border-red-700 bg-red-700 px-4 text-sm font-semibold text-white hover:bg-red-800 disabled:cursor-wait disabled:opacity-50">{{ __t('export_pdf', 'reports') }}</button>
            </x-slot:actions>
        </x-page-header>
    </div>
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
                <div class="space-y-3 md:hidden">
                    @foreach ($products as $product)
                        <x-mobile-record-card data-report-mobile-card="inventory" :title="$product->name" :subtitle="'SKU: '.$product->sku">
                            <x-slot:status>
                                <x-badge :variant="$product->is_active ? 'success' : 'neutral'">{{ __t($product->is_active ? 'active' : 'inactive', 'reports') }}</x-badge>
                                @if ($product->stock <= $product->minimum_stock)
                                    <x-badge variant="warning">{{ __t('low_stock', 'reports') }}</x-badge>
                                @endif
                            </x-slot:status>
                            <x-slot:meta>
                                <div class="col-span-2 min-w-0"><dt class="text-xs text-primary-500">{{ __t('categories', 'reports') }}</dt><dd class="mt-1 break-words font-medium text-primary-900">{{ $product->categories->pluck('name')->join(', ') ?: '—' }}</dd></div>
                                <div><dt class="text-xs text-primary-500">{{ __t('stock', 'reports') }}</dt><dd class="mt-1 font-medium text-primary-900">{{ $product->stock }}</dd></div>
                                <div><dt class="text-xs text-primary-500">{{ __t('unit_cost', 'reports') }}</dt><dd class="mt-1 font-medium text-primary-900">{{ $currency['symbol'] }}{{ number_format((float) $product->cost, 2) }}</dd></div>
                                <div class="col-span-2"><dt class="text-xs text-primary-500">{{ __t('inventory_value', 'reports') }}</dt><dd class="mt-1 font-semibold text-primary-950">{{ $currency['symbol'] }}{{ number_format($product->stock * (float) $product->cost, 2) }}</dd></div>
                            </x-slot:meta>
                        </x-mobile-record-card>
                    @endforeach
                </div>
                <div class="-mx-5 hidden overflow-x-auto md:block" data-report-desktop-table="inventory"><table class="min-w-full divide-y divide-primary-200 text-sm"><thead class="bg-primary-50 text-left text-xs font-semibold uppercase text-primary-500"><tr><th class="px-5 py-3">{{ __t('product', 'reports') }}</th><th class="px-5 py-3">{{ __t('categories', 'reports') }}</th><th class="px-5 py-3 text-right">{{ __t('stock', 'reports') }}</th><th class="px-5 py-3 text-right">{{ __t('unit_cost', 'reports') }}</th><th class="px-5 py-3 text-right">{{ __t('inventory_value', 'reports') }}</th><th class="px-5 py-3">{{ __t('status', 'reports') }}</th></tr></thead><tbody class="divide-y divide-primary-100">@foreach ($products as $product)<tr><td class="px-5 py-3"><p class="font-semibold text-primary-900">{{ $product->name }}</p><p class="text-xs text-primary-500">{{ $product->sku }}</p></td><td class="px-5 py-3 text-primary-600">{{ $product->categories->pluck('name')->join(', ') ?: '—' }}</td><td class="px-5 py-3 text-right"><x-badge :variant="$product->stock <= $product->minimum_stock ? 'warning' : 'success'">{{ $product->stock }}</x-badge></td><td class="px-5 py-3 text-right">{{ $currency['symbol'] }}{{ number_format((float) $product->cost, 2) }}</td><td class="px-5 py-3 text-right font-semibold">{{ $currency['symbol'] }}{{ number_format($product->stock * (float) $product->cost, 2) }}</td><td class="px-5 py-3"><x-badge :variant="$product->is_active ? 'success' : 'neutral'">{{ __t($product->is_active ? 'active' : 'inactive', 'reports') }}</x-badge></td></tr>@endforeach</tbody></table></div>
                <div class="mt-4">{{ $products->links() }}</div>
            @endif
        </x-card>
    </div>
</div>
