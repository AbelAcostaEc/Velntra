<div class="space-y-6">
    {{-- Navegación superior y Encabezado del Cliente --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a
                href="{{ route('customers.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-500 hover:text-brand-600 transition mb-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                {{ __t('back_to_customers', 'customers') }}
            </a>

            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-primary-950 font-heading">
                    {{ $customer->name }}
                </h1>
                @if ($customer->isConsumidorFinal())
                    <span class="rounded-md bg-primary-100 px-2 py-0.5 text-xs font-bold text-primary-700 uppercase">
                        {{ __t('final_consumer_badge', 'customers') }}
                    </span>
                @endif
                <x-badge :variant="$customer->is_active ? 'success' : 'neutral'">
                    {{ $customer->is_active ? __t('active', 'customers') : __t('inactive', 'customers') }}
                </x-badge>
            </div>

            <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-primary-500">
                @if ($customer->document)
                    <span class="font-mono">Doc: {{ $customer->document }}</span>
                @endif
                @if ($customer->phone)
                    <span>Telf: {{ $customer->phone }}</span>
                @endif
                @if ($customer->email)
                    <span>Email: {{ $customer->email }}</span>
                @endif
                @if ($customer->address)
                    <span>Dir: {{ $customer->address }}</span>
                @endif
            </div>
        </div>

        <div>
            <a
                href="{{ route('sales.index') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-brand-700 transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                Nueva Venta POS
            </a>
        </div>
    </div>

    {{-- Tarjetas de Estadísticas de Compra --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Invertido --}}
        <div class="rounded-2xl border border-primary-200 bg-white/80 p-4 shadow-xs backdrop-blur-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-primary-500">{{ __t('total_spent', 'customers') }}</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2">
                <span class="font-mono text-2xl font-black text-primary-950">
                    ${{ number_format($stats['total_spent'], 2) }}
                </span>
                <p class="text-[11px] text-primary-400 mt-0.5">{{ __t('completed_sales_only', 'customers') }}</p>
            </div>
        </div>

        {{-- Cantidad de Compras --}}
        <div class="rounded-2xl border border-primary-200 bg-white/80 p-4 shadow-xs backdrop-blur-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-primary-500">{{ __t('total_purchases', 'customers') }}</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2">
                <span class="font-mono text-2xl font-black text-primary-950">
                    {{ $stats['total_purchases'] }}
                </span>
                <p class="text-[11px] text-primary-400 mt-0.5">Transacciones registradas</p>
            </div>
        </div>

        {{-- Ticket Promedio --}}
        <div class="rounded-2xl border border-primary-200 bg-white/80 p-4 shadow-xs backdrop-blur-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-primary-500">{{ __t('average_ticket', 'customers') }}</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2">
                <span class="font-mono text-2xl font-black text-primary-950">
                    ${{ number_format($stats['average_ticket'], 2) }}
                </span>
                <p class="text-[11px] text-primary-400 mt-0.5">Promedio por compra</p>
            </div>
        </div>

        {{-- Última Compra --}}
        <div class="rounded-2xl border border-primary-200 bg-white/80 p-4 shadow-xs backdrop-blur-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-primary-500">{{ __t('last_purchase', 'customers') }}</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                    </svg>
                </span>
            </div>
            <div class="mt-2">
                @if ($stats['last_purchase'])
                    <span class="text-sm font-bold text-primary-950">
                        {{ $stats['last_purchase']->created_at->format('d/m/Y') }}
                    </span>
                    <p class="text-[11px] text-primary-400 mt-0.5">
                        {{ $stats['last_purchase']->created_at->diffForHumans() }}
                    </p>
                @else
                    <span class="text-sm font-medium text-primary-400">
                        {{ __t('no_purchases_yet', 'customers') }}
                    </span>
                    <p class="text-[11px] text-primary-400 mt-0.5">—</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabla de Historial de Compras --}}
    <x-table loading-target="search, statusFilter, perPage">
        <x-slot:toolbar>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:max-w-xl">
                    <x-table-search
                        placeholder="{{ __t('search_sales_placeholder', 'customers') }}"
                        wire:model.live.debounce.300ms="search"
                        class="w-full sm:max-w-xs" />

                    <select
                        wire:model.live="statusFilter"
                        class="rounded-xl border border-primary-200 bg-white/80 px-3 py-2 text-sm text-primary-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <option value="">{{ __t('all_statuses', 'customers') }}</option>
                        <option value="completed">Completadas</option>
                        <option value="pending">En Espera</option>
                        <option value="cancelled">Anuladas</option>
                    </select>
                </div>

                <x-per-page-select wire:model.live="perPage" />
            </div>
        </x-slot:toolbar>

        <x-slot:head>
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_sale_number', 'customers') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_sale_date', 'customers') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">Cajero / Vendedor</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_sale_items', 'customers') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_sale_payment', 'customers') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_sale_status', 'customers') }}</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_sale_total', 'customers') }}</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_actions', 'customers') }}</th>
            </tr>
        </x-slot:head>

        @forelse ($sales as $sale)
            <tr class="hover:bg-primary-50/50 transition-colors">
                <td class="px-4 py-4 text-sm font-mono font-bold text-primary-950">
                    {{ $sale->number }}
                </td>
                <td class="px-4 py-4 text-xs text-primary-600">
                    {{ $sale->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="px-4 py-4 text-xs text-primary-700">
                    {{ $sale->user?->name ?: '—' }}
                </td>
                <td class="px-4 py-4 text-xs text-primary-700">
                    {{ $sale->items->sum('quantity') }} prod.
                </td>
                <td class="px-4 py-4 text-xs capitalize text-primary-700">
                    {{ $sale->payment_method }}
                </td>
                <td class="px-4 py-4">
                    @if ($sale->status === 'completed')
                        <x-badge variant="success">Completada</x-badge>
                    @elseif ($sale->status === 'pending')
                        <x-badge variant="warning">En Espera</x-badge>
                    @else
                        <x-badge variant="danger">Anulada</x-badge>
                    @endif
                </td>
                <td class="px-4 py-4 text-right font-mono text-sm font-bold text-primary-950">
                    ${{ number_format($sale->total, 2) }}
                </td>
                <td class="px-4 py-4 text-right">
                    <x-button
                        variant="secondary"
                        size="sm"
                        wire:click="openReceiptModal({{ $sale->id }})">
                        <svg class="h-3.5 w-3.5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        {{ __t('view_receipt', 'customers') }}
                    </x-button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">
                    <x-empty-state
                        :title="__t('no_sales_found', 'customers')"
                        :description="__t('no_sales_found_description', 'customers')" />
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            <div class="px-4 py-3">
                {{ $sales->links() }}
            </div>
        </x-slot:pagination>
    </x-table>

    {{-- MODAL DE COMPROBANTE / RECIBO DETALLADO --}}
    <x-modal name="customer-receipt-modal" title="Comprobante de Venta" description="Detalle de productos y valores de la transacción.">
        <div class="p-6 space-y-4">
            @if ($selectedSale)
                <div id="printable-ticket" class="rounded-xl border border-dashed border-primary-300 bg-white p-5 text-primary-900 font-mono text-xs space-y-3">
                    <div class="text-center border-b border-primary-200 pb-3">
                        <h3 class="font-bold text-sm tracking-wider uppercase">{{ $settings->company_name }}</h3>
                        @if ($settings->ruc)
                            <p class="text-[11px] text-primary-600">RUC: {{ $settings->ruc }}</p>
                        @endif
                        @if ($settings->address)
                            <p class="text-[10px] text-primary-500">{{ $settings->address }}</p>
                        @endif
                        @if ($settings->phone)
                            <p class="text-[10px] text-primary-500">Telf: {{ $settings->phone }}</p>
                        @endif
                    </div>

                    <div class="flex justify-between text-[11px] border-b border-primary-200 pb-2">
                        <div>
                            <p><span class="text-primary-500">Ticket:</span> #{{ $selectedSale->number }}</p>
                            <p><span class="text-primary-500">Cliente:</span> {{ $selectedSale->customer->name }}</p>
                            @if ($selectedSale->customer->document)
                                <p><span class="text-primary-500">Doc:</span> {{ $selectedSale->customer->document }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p>{{ $selectedSale->created_at->format('d/m/Y') }}</p>
                            <p>{{ $selectedSale->created_at->format('H:i') }}</p>
                        </div>
                    </div>

                    {{-- Tabla de productos --}}
                    <table class="w-full text-left text-[11px]">
                        <thead>
                            <tr class="border-b border-primary-200 text-primary-500">
                                <th class="pb-1">Cant.</th>
                                <th class="pb-1">Producto</th>
                                <th class="pb-1 text-right">Precio</th>
                                <th class="pb-1 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary-100">
                            @foreach ($selectedSale->items as $it)
                                <tr>
                                    <td class="py-1">{{ $it->quantity }}x</td>
                                    <td class="py-1 truncate max-w-[140px]">{{ $it->product->name }}</td>
                                    <td class="py-1 text-right">${{ number_format($it->price, 2) }}</td>
                                    <td class="py-1 text-right font-bold">${{ number_format($it->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Totales --}}
                    <div class="border-t border-primary-200 pt-2 space-y-1 text-right text-[11px]">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>${{ number_format($selectedSale->subtotal, 2) }}</span>
                        </div>
                        @if ($selectedSale->discount > 0)
                            <div class="flex justify-between text-rose-600">
                                <span>Descuento:</span>
                                <span>-${{ number_format($selectedSale->discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span>IVA ({{ $selectedSale->tax_percentage }}%):</span>
                            <span>${{ number_format($selectedSale->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-sm border-t border-primary-300 pt-1">
                            <span>TOTAL:</span>
                            <span>${{ number_format($selectedSale->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-[10px] text-primary-500 pt-1">
                            <span>Pago ({{ ucfirst($selectedSale->payment_method) }}):</span>
                            <span>${{ number_format($selectedSale->amount_paid ?: $selectedSale->total, 2) }}</span>
                        </div>
                        @if ($selectedSale->change > 0)
                            <div class="flex justify-between text-[10px] text-emerald-700 font-bold">
                                <span>Cambio:</span>
                                <span>${{ number_format($selectedSale->change, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="flex justify-end gap-2 pt-2 border-t border-primary-200">
                <x-button
                    type="button"
                    variant="secondary"
                    onclick="window.print()">
                    <svg class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                    </svg>
                    Imprimir Ticket
                </x-button>

                <x-button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close-modal', 'customer-receipt-modal')">
                    Cerrar
                </x-button>
            </div>
        </div>
    </x-modal>
</div>
