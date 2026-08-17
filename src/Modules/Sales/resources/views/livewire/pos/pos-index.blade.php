<div>
    {{-- Barra superior de pestañas y ventas en espera --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2 rounded-2xl border border-primary-200 bg-white/80 p-1.5 shadow-xs backdrop-blur-sm">
            <button
                type="button"
                wire:click="$set('activeTab', 'pos')"
                class="flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'pos' ? 'bg-brand-600 text-white shadow-xs' : 'text-primary-600 hover:text-primary-900 hover:bg-primary-50' }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                {{ __t('pos', 'sales') }}
            </button>

            <button
                type="button"
                wire:click="$set('activeTab', 'history')"
                class="flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'history' ? 'bg-brand-600 text-white shadow-xs' : 'text-primary-600 hover:text-primary-900 hover:bg-primary-50' }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ __t('sales_history', 'sales') }}
            </button>
        </div>

        {{-- Botón de ventas en espera / pausadas --}}
        <div class="flex items-center gap-2">
            <button
                type="button"
                x-on:click="$dispatch('open-modal', 'held-sales-modal')"
                class="relative inline-flex items-center gap-2 rounded-2xl border border-amber-200 bg-amber-50/80 px-4 py-2.5 text-sm font-semibold text-amber-900 shadow-xs transition hover:bg-amber-100 hover:border-amber-300">
                <svg class="h-4 w-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>{{ __t('pending_sales', 'sales') }}</span>
                @if ($heldCount > 0)
                    <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-600 px-1.5 text-xs font-bold text-white shadow-xs">
                        {{ $heldCount }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    {{-- VISTA 1: PUNTO DE VENTA (POS) --}}
    @if ($activeTab === 'pos')
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-start">
            
            {{-- Columna Izquierda: Catálogo de Productos y Escáner (7 columnas) --}}
            <div class="lg:col-span-7 space-y-4">
                
                {{-- Barra de Búsqueda y Filtros de Categoría --}}
                <div class="rounded-2xl border border-primary-200 bg-white/90 p-4 shadow-sm backdrop-blur-sm space-y-3">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-primary-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            wire:model.live.debounce.250ms="searchProduct"
                            wire:keydown.enter="scanBarcode"
                            placeholder="{{ __t('search_products_placeholder', 'sales') }}"
                            autofocus
                            class="w-full rounded-xl border border-primary-200 bg-primary-50/50 py-2.5 pl-10 pr-24 text-sm text-primary-900 placeholder:text-primary-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2.5">
                            <span class="inline-flex items-center gap-1 rounded-md bg-primary-200/60 px-2 py-0.5 text-[10px] font-semibold text-primary-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ __t('barcode_scan_ready', 'sales') }}
                            </span>
                        </div>
                    </div>

                    {{-- Píldoras de Filtro por Categoría --}}
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                        <button
                            type="button"
                            wire:click="$set('categoryFilter', '')"
                            class="shrink-0 rounded-lg px-3 py-1.5 font-medium transition {{ empty($categoryFilter) ? 'bg-primary-900 text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' }}">
                            {{ __t('all_categories', 'sales') }}
                        </button>
                        @foreach ($categories as $cat)
                            <button
                                type="button"
                                wire:click="$set('categoryFilter', '{{ $cat->id }}')"
                                class="shrink-0 rounded-lg px-3 py-1.5 font-medium transition {{ $categoryFilter == $cat->id ? 'bg-primary-900 text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' }}">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Cuadrícula de Productos --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                    @forelse ($products as $prod)
                        <button
                            type="button"
                            wire:click="addToCart({{ $prod->id }})"
                            @disabled($prod->stock <= 0)
                            class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-primary-200 bg-white p-3 text-left shadow-xs transition hover:border-brand-400 hover:shadow-md active:scale-98 disabled:opacity-60 disabled:pointer-events-none">
                            
                            {{-- Imagen o Placeholder --}}
                            <div class="relative mb-2 flex h-24 w-full items-center justify-center overflow-hidden rounded-xl bg-primary-50 p-1">
                                @if ($prod->image_url)
                                    <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="h-full w-full object-contain transition duration-300 group-hover:scale-105" />
                                @else
                                    <svg class="h-8 w-8 text-primary-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                    </svg>
                                @endif

                                {{-- Badge de Stock --}}
                                <div class="absolute top-1.5 right-1.5">
                                    <span class="inline-flex rounded-md px-1.5 py-0.5 text-[10px] font-bold {{ $prod->stock > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $prod->stock > 0 ? $prod->stock : __t('out_of_stock', 'sales') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Info del producto --}}
                            <div>
                                <h3 class="line-clamp-2 text-xs font-semibold text-primary-900 group-hover:text-brand-600" title="{{ $prod->name }}">
                                    {{ $prod->name }}
                                </h3>
                                <p class="mt-0.5 text-[10px] text-primary-400 font-mono">{{ $prod->sku }}</p>
                            </div>

                            <div class="mt-2 flex items-center justify-between border-t border-primary-100 pt-2">
                                <span class="text-sm font-bold text-primary-950">
                                    ${{ number_format($prod->price, 2) }}
                                </span>
                                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-brand-50 text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </span>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full py-12 text-center">
                            <x-empty-state
                                :title="__t('no_products_found', 'sales')"
                                :description="__t('no_products_description', 'sales')" />
                        </div>
                    @endforelse
                </div>

                {{-- Paginación de Productos --}}
                <div class="pt-2">
                    {{ $products->links() }}
                </div>
            </div>

            {{-- Columna Derecha: Carrito de Compras, Cliente y Totales (5 columnas) --}}
            <div class="lg:col-span-5 space-y-4">
                
                {{-- Selector de Cliente --}}
                <div class="rounded-2xl border border-primary-200 bg-white/90 p-4 shadow-sm backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-primary-900 truncate">
                                        {{ $selectedCustomer ? $selectedCustomer->name : __t('select_customer', 'sales') }}
                                    </span>
                                    @if ($selectedCustomer?->isConsumidorFinal())
                                        <span class="rounded bg-primary-100 px-1.5 py-0.5 text-[9px] font-bold text-primary-700 uppercase">Final</span>
                                    @endif
                                </div>
                                @if ($selectedCustomer?->document)
                                    <p class="text-[10px] text-primary-500 font-mono">{{ $selectedCustomer->document }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <button
                                type="button"
                                x-on:click="$dispatch('open-modal', 'select-customer-modal')"
                                class="rounded-lg border border-primary-200 bg-white px-2.5 py-1 text-xs font-semibold text-primary-700 shadow-2xs hover:bg-primary-50">
                                Cambiar
                            </button>
                            <button
                                type="button"
                                x-on:click="$dispatch('open-modal', 'quick-customer-modal')"
                                class="rounded-lg bg-brand-50 px-2 py-1 text-xs font-semibold text-brand-700 hover:bg-brand-100">
                                + Nuevo
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Panel del Carrito --}}
                <div class="flex flex-col justify-between rounded-2xl border border-primary-200 bg-white/90 p-4 shadow-sm backdrop-blur-sm min-h-[480px]">
                    
                    {{-- Cabecera del Carrito --}}
                    <div class="flex items-center justify-between border-b border-primary-100 pb-3">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-primary-900">{{ __t('cart', 'sales') }}</h2>
                            <span class="rounded-full bg-primary-100 px-2 py-0.5 text-xs font-semibold text-primary-700">
                                {{ $totals['items_count'] }} {{ __t('items_count', 'sales') }}
                            </span>
                            @if ($resumedSaleId)
                                <span class="rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800 animate-pulse">
                                    Reanudada
                                </span>
                            @endif
                        </div>

                        @if (!empty($cart))
                            <button
                                type="button"
                                wire:click="clearCart"
                                class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition">
                                {{ __t('clear_cart', 'sales') }}
                            </button>
                        @endif
                    </div>

                    {{-- Lista de Ítems del Carrito --}}
                    <div class="my-3 flex-1 overflow-y-auto space-y-2.5 max-h-[260px] pr-1">
                        @forelse ($cart as $item)
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-primary-100 bg-primary-50/40 p-2.5 transition hover:bg-primary-50">
                                <div class="min-w-0 flex-1">
                                    <h4 class="truncate text-xs font-semibold text-primary-900" title="{{ $item['name'] }}">
                                        {{ $item['name'] }}
                                    </h4>
                                    <p class="text-[10px] text-primary-500">
                                        ${{ number_format($item['price'], 2) }} c/u
                                    </p>
                                </div>

                                {{-- Control de Cantidad --}}
                                <div class="flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        wire:click="updateQuantity({{ $item['product_id'] }}, {{ $item['quantity'] - 1 }})"
                                        class="flex h-6 w-6 items-center justify-center rounded-md border border-primary-200 bg-white text-xs font-bold text-primary-700 hover:bg-primary-100">
                                        -
                                    </button>
                                    <span class="w-7 text-center font-mono text-xs font-bold text-primary-900">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button
                                        type="button"
                                        wire:click="updateQuantity({{ $item['product_id'] }}, {{ $item['quantity'] + 1 }})"
                                        class="flex h-6 w-6 items-center justify-center rounded-md border border-primary-200 bg-white text-xs font-bold text-primary-700 hover:bg-primary-100">
                                        +
                                    </button>
                                </div>

                                {{-- Subtotal del ítem y botón de borrar --}}
                                <div class="flex items-center gap-2 text-right">
                                    <span class="font-mono text-xs font-bold text-primary-950">
                                        ${{ number_format($item['subtotal'], 2) }}
                                    </span>
                                    <button
                                        type="button"
                                        wire:click="removeFromCart({{ $item['product_id'] }})"
                                        class="text-primary-400 hover:text-rose-600 transition">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <svg class="h-10 w-10 text-primary-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                </svg>
                                <p class="mt-2 text-xs font-medium text-primary-500">{{ __t('empty_cart', 'sales') }}</p>
                                <p class="text-[10px] text-primary-400 max-w-[200px]">{{ __t('empty_cart_help', 'sales') }}</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Totales y Acciones del Carrito --}}
                    <div class="border-t border-primary-100 pt-3 space-y-2">
                        <div class="flex items-center justify-between text-xs text-primary-600">
                            <span>{{ __t('subtotal', 'sales') }}</span>
                            <span class="font-mono font-medium">${{ number_format($totals['subtotal'], 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs text-primary-600">
                            <span class="flex items-center gap-1">
                                {{ __t('discount', 'sales') }}
                                <input
                                    type="number"
                                    step="0.5"
                                    min="0"
                                    wire:model.live.debounce.300ms="discount"
                                    class="w-16 rounded border border-primary-200 px-1.5 py-0.5 text-right text-xs text-primary-900 focus:outline-none focus:ring-1 focus:ring-brand-500"
                                />
                            </span>
                            <span class="font-mono font-medium text-rose-600">-${{ number_format($totals['discount'], 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs text-primary-600">
                            <span>{{ __t('tax', 'sales') }} ({{ $totals['tax_percentage'] }}%)</span>
                            <span class="font-mono font-medium">${{ number_format($totals['tax'], 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between border-t border-primary-200 pt-2">
                            <span class="text-sm font-bold text-primary-900">{{ __t('total', 'sales') }}</span>
                            <span class="font-mono text-xl font-black text-brand-600">
                                ${{ number_format($totals['total'], 2) }}
                            </span>
                        </div>

                        {{-- Botones de Acción del Carrito --}}
                        <div class="grid grid-cols-2 gap-2 pt-2">
                            <button
                                type="button"
                                wire:click="holdCurrentSale"
                                @disabled(empty($cart))
                                class="flex items-center justify-center gap-1.5 rounded-xl border border-amber-300 bg-amber-50 px-3 py-2.5 text-xs font-bold text-amber-900 shadow-2xs hover:bg-amber-100 disabled:opacity-50 disabled:pointer-events-none transition">
                                <svg class="h-4 w-4 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ __t('hold_sale', 'sales') }}
                            </button>

                            <button
                                type="button"
                                wire:click="openPaymentModal"
                                @disabled(empty($cart))
                                class="flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 active:scale-98 disabled:opacity-50 disabled:pointer-events-none transition">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v10.5m0-10.5h19.5m0 0v10.5m0-10.5v-.75a.75.75 0 0 0-.75-.75h-.75m0 0H3.75m19.5 0a60.07 60.07 0 0 0-15.797 2.101c-.727.198-1.453-.342-1.453-1.096V4.5" />
                                </svg>
                                {{ __t('checkout_btn', 'sales') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- VISTA 2: HISTORIAL DE VENTAS --}}
    @if ($activeTab === 'history')
        <div class="space-y-4">
            <x-table loading-target="historySearch, historyStatusFilter, perPage">
                <x-slot:toolbar>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:max-w-xl">
                            <x-table-search placeholder="Buscar por número de venta o cliente..." wire:model.live.debounce.300ms="historySearch" class="w-full sm:max-w-xs" />
                            
                            <select
                                wire:model.live="historyStatusFilter"
                                class="rounded-xl border border-primary-200 bg-white/80 px-3 py-2 text-sm text-primary-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                                <option value="">Todos los estados</option>
                                <option value="completed">{{ __t('status_completed', 'sales') }}</option>
                                <option value="pending">{{ __t('status_pending', 'sales') }}</option>
                                <option value="cancelled">{{ __t('status_cancelled', 'sales') }}</option>
                            </select>
                        </div>
                        <x-per-page-select wire:model.live="perPage" />
                    </div>
                </x-slot:toolbar>

                <x-slot:head>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_number', 'sales') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_customer', 'sales') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_date', 'sales') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_payment', 'sales') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_status', 'sales') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_total', 'sales') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_actions', 'sales') }}</th>
                    </tr>
                </x-slot:head>

                @if ($historySales)
                    @forelse ($historySales as $sale)
                        <tr class="hover:bg-primary-50/50 transition-colors">
                            <td class="px-4 py-4 text-sm font-mono font-bold text-primary-950">
                                {{ $sale->number }}
                            </td>
                            <td class="px-4 py-4 text-sm font-medium text-primary-900">
                                {{ $sale->customer->name }}
                            </td>
                            <td class="px-4 py-4 text-xs text-primary-600">
                                {{ $sale->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-4 text-xs capitalize text-primary-700">
                                {{ $sale->payment_method }}
                            </td>
                            <td class="px-4 py-4">
                                @if ($sale->status === 'completed')
                                    <x-badge variant="success">{{ __t('status_completed', 'sales') }}</x-badge>
                                @elseif ($sale->status === 'pending')
                                    <x-badge variant="warning">{{ __t('status_pending', 'sales') }}</x-badge>
                                @else
                                    <x-badge variant="danger">{{ __t('status_cancelled', 'sales') }}</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right font-mono text-sm font-bold text-primary-950">
                                ${{ number_format($sale->total, 2) }}
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="$set('completedSaleId', {{ $sale->id }})"
                                        x-on:click="$dispatch('open-modal', 'receipt-modal')">
                                        Ticket
                                    </x-button>

                                    @can('cancel', $sale)
                                        <x-button
                                            variant="danger"
                                            size="sm"
                                            wire:click="openCancelModal({{ $sale->id }})">
                                            {{ __t('cancel_sale', 'sales') }}
                                        </x-button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state
                                    :title="__t('no_sales_title', 'sales')"
                                    :description="__t('no_sales_description', 'sales')" />
                            </td>
                        </tr>
                    @endforelse
                @endif

                <x-slot:pagination>
                    @if ($historySales)
                        <div class="px-4 py-3">
                            {{ $historySales->links() }}
                        </div>
                    @endif
                </x-slot:pagination>
            </x-table>
        </div>
    @endif

    {{-- MODAL 1: PROCESAR COBRO (PAYMENT MODAL) --}}
    <x-modal name="payment-modal" :title="__t('payment_modal_title', 'sales')" :description="__t('payment_modal_description', 'sales')">
        <form wire:submit.prevent="processPayment" class="p-6 space-y-5">
            {{-- Gran Total a Pagar --}}
            <div class="flex flex-col items-center justify-center rounded-2xl bg-brand-50/80 p-5 border border-brand-200">
                <span class="text-xs uppercase font-bold tracking-wider text-brand-700">{{ __t('total', 'sales') }}</span>
                <span class="font-mono text-3xl font-black text-brand-950 mt-1">
                    ${{ number_format($totals['total'], 2) }}
                </span>
            </div>

            {{-- Selección de Método de Pago --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-primary-700 mb-2">
                    {{ __t('payment_method', 'sales') }}
                </label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach (['cash' => 'Efectivo', 'card' => 'Tarjeta', 'transfer' => 'Transferencia'] as $key => $label)
                        <button
                            type="button"
                            wire:click="$set('paymentMethod', '{{ $key }}')"
                            class="flex flex-col items-center justify-center rounded-xl p-3 border text-xs font-bold transition {{ $paymentMethod === $key ? 'border-brand-600 bg-brand-50 text-brand-900 shadow-xs ring-2 ring-brand-500/20' : 'border-primary-200 bg-white text-primary-700 hover:bg-primary-50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Importe Recibido y Botones de Denominación (Solo Efectivo) --}}
            @if ($paymentMethod === 'cash')
                <div class="space-y-3">
                    <x-input
                        :label="__t('amount_paid', 'sales')"
                        name="amountPaid"
                        type="number"
                        step="0.01"
                        wire:model.live="amountPaid"
                        required />

                    {{-- Botones Rápidos de Billetes --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            wire:click="setExactAmount"
                            class="rounded-lg border border-primary-200 bg-primary-100/70 px-2.5 py-1 text-xs font-bold text-primary-800 hover:bg-primary-200">
                            {{ __t('exact_amount', 'sales') }}
                        </button>
                        @foreach ([5, 10, 20, 50, 100] as $bill)
                            @if ($bill >= $totals['total'])
                                <button
                                    type="button"
                                    wire:click="setQuickAmount({{ $bill }})"
                                    class="rounded-lg border border-primary-200 bg-white px-2.5 py-1 text-xs font-bold text-primary-800 hover:bg-primary-50">
                                    ${{ $bill }}
                                </button>
                            @endif
                        @endforeach
                    </div>

                    {{-- Cambio / Vuelto --}}
                    <div class="flex items-center justify-between rounded-xl bg-emerald-50 p-3 border border-emerald-200">
                        <span class="text-xs font-bold text-emerald-900">{{ __t('change', 'sales') }}:</span>
                        <span class="font-mono text-lg font-black text-emerald-950">
                            ${{ number_format(max(0.00, ((float)$amountPaid) - $totals['total']), 2) }}
                        </span>
                    </div>
                </div>
            @endif

            {{-- Notas adicionales --}}
            <div>
                <x-input
                    :label="__t('notes', 'sales')"
                    name="notes"
                    :placeholder="__t('notes_placeholder', 'sales')"
                    wire:model="notes" />
            </div>

            {{-- Botones de Acción --}}
            <div class="flex justify-end gap-2 border-t border-primary-200 pt-4">
                <x-button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close-modal', 'payment-modal')">
                    {{ __t('cancel', 'sales') }}
                </x-button>

                <x-button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __t('complete_sale_btn', 'sales') }}</span>
                    <span wire:loading>{{ __t('processing', 'sales') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL 2: BANDEJA DE VENTAS EN ESPERA (HELD SALES MODAL) --}}
    <x-modal name="held-sales-modal" :title="__t('held_sales_title', 'sales')" :description="__t('held_sales_description', 'sales')">
        <div class="p-6 space-y-4">
            @forelse ($heldSales as $held)
                <div class="flex items-center justify-between rounded-xl border border-amber-200 bg-amber-50/50 p-4 transition hover:bg-amber-50">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-sm text-amber-950">{{ $held->number }}</span>
                            <span class="text-xs font-medium text-primary-700">• {{ $held->customer->name }}</span>
                        </div>
                        <p class="text-xs text-primary-500 mt-0.5">
                            {{ $held->items->sum('quantity') }} productos • Pausada hace {{ $held->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="font-mono text-base font-black text-primary-950">
                            ${{ number_format($held->total, 2) }}
                        </span>

                        <x-button
                            type="button"
                            variant="secondary"
                            size="sm"
                            wire:click="resumeHeldSale({{ $held->id }})">
                            {{ __t('resume_sale', 'sales') }}
                        </x-button>

                        <button
                            type="button"
                            wire:click="deleteHeldSale({{ $held->id }})"
                            class="text-rose-500 hover:text-rose-700 p-1"
                            title="Descartar">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-primary-400">
                    <p class="text-sm font-medium">{{ __t('no_held_sales', 'sales') }}</p>
                </div>
            @endforelse

            <div class="flex justify-end pt-3 border-t border-primary-200">
                <x-button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close-modal', 'held-sales-modal')">
                    {{ __t('cancel', 'sales') }}
                </x-button>
            </div>
        </div>
    </x-modal>

    {{-- MODAL 3: COMPROBANTE DE VENTA / TICKET --}}
    <x-modal name="receipt-modal" :title="__t('receipt_title', 'sales')" description="Comprobante generado">
        <div class="p-6 space-y-4">
            @if ($completedSale)
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
                            <p><span class="text-primary-500">Ticket:</span> #{{ $completedSale->number }}</p>
                            <p><span class="text-primary-500">Cliente:</span> {{ $completedSale->customer->name }}</p>
                            @if ($completedSale->customer->document)
                                <p><span class="text-primary-500">Doc:</span> {{ $completedSale->customer->document }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p>{{ $completedSale->created_at->format('d/m/Y') }}</p>
                            <p>{{ $completedSale->created_at->format('H:i') }}</p>
                        </div>
                    </div>

                    {{-- Tabla de productos del recibo --}}
                    <table class="w-full text-left text-[11px]">
                        <thead>
                            <tr class="border-b border-primary-200 text-primary-500">
                                <th class="pb-1">Cant.</th>
                                <th class="pb-1">Prod.</th>
                                <th class="pb-1 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary-100">
                            @foreach ($completedSale->items as $it)
                                <tr>
                                    <td class="py-1">{{ $it->quantity }}x</td>
                                    <td class="py-1 truncate max-w-[140px]">{{ $it->product->name }}</td>
                                    <td class="py-1 text-right font-bold">${{ number_format($it->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Totales del recibo --}}
                    <div class="border-t border-primary-200 pt-2 space-y-1 text-right text-[11px]">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>${{ number_format($completedSale->subtotal, 2) }}</span>
                        </div>
                        @if ($completedSale->discount > 0)
                            <div class="flex justify-between text-rose-600">
                                <span>Descuento:</span>
                                <span>-${{ number_format($completedSale->discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span>IVA ({{ $completedSale->tax_percentage }}%):</span>
                            <span>${{ number_format($completedSale->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-sm border-t border-primary-300 pt-1">
                            <span>TOTAL:</span>
                            <span>${{ number_format($completedSale->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-[10px] text-primary-500 pt-1">
                            <span>Pago ({{ ucfirst($completedSale->payment_method) }}):</span>
                            <span>${{ number_format($completedSale->amount_paid ?: $completedSale->total, 2) }}</span>
                        </div>
                        @if ($completedSale->change > 0)
                            <div class="flex justify-between text-[10px] text-emerald-700 font-bold">
                                <span>Cambio:</span>
                                <span>${{ number_format($completedSale->change, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="text-center pt-3 border-t border-primary-200 text-[10px] text-primary-500">
                        <p>{{ __t('thank_you_message', 'sales') }}</p>
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
                    {{ __t('print_ticket', 'sales') }}
                </x-button>

                <x-button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'receipt-modal')">
                    {{ __t('new_sale', 'sales') }}
                </x-button>
            </div>
        </div>
    </x-modal>

    {{-- MODAL 4: BUSCAR Y SELECCIONAR CLIENTE --}}
    <x-modal name="select-customer-modal" title="Seleccionar Cliente" description="Busca un cliente registrado para asociarlo a la venta.">
        <div class="p-6 space-y-4">
            <x-input
                name="customerSearch"
                placeholder="Escribe el nombre o documento del cliente..."
                wire:model.live.debounce.250ms="customerSearch"
                autofocus />

            <div class="max-h-60 overflow-y-auto divide-y divide-primary-100 rounded-xl border border-primary-200">
                @forelse ($customersList as $cust)
                    <button
                        type="button"
                        wire:click="selectCustomer({{ $cust->id }})"
                        class="flex w-full items-center justify-between p-3 text-left transition hover:bg-primary-50">
                        <div>
                            <p class="text-sm font-semibold text-primary-900">{{ $cust->name }}</p>
                            <p class="text-xs text-primary-500">{{ $cust->document ?: 'Sin documento' }} • {{ $cust->phone ?: 'Sin teléfono' }}</p>
                        </div>
                        <span class="rounded-md bg-brand-50 px-2 py-1 text-xs font-semibold text-brand-700">Seleccionar</span>
                    </button>
                @empty
                    <div class="p-4 text-center text-xs text-primary-400">
                        {{ empty($customerSearch) ? 'Escribe al menos 1 caracter para buscar...' : 'No se encontraron clientes coincidentes.' }}
                    </div>
                @endforelse
            </div>

            <div class="flex justify-end pt-2">
                <x-button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close-modal', 'select-customer-modal')">
                    {{ __t('cancel', 'sales') }}
                </x-button>
            </div>
        </div>
    </x-modal>

    {{-- MODAL 5: CREACIÓN RÁPIDA DE CLIENTE --}}
    <x-modal name="quick-customer-modal" title="Crear Cliente Rápido" description="Registra un nuevo cliente sin salir del punto de venta.">
        <form wire:submit.prevent="createQuickCustomer" class="p-6 space-y-4">
            <x-input label="Nombre Completo" name="newCustomerName" placeholder="Ej. Carlos Mendoza" wire:model="newCustomerName" required />
            <x-input label="Cédula / RUC" name="newCustomerDocument" placeholder="Ej. 1712345678" wire:model="newCustomerDocument" />
            <div class="grid grid-cols-2 gap-3">
                <x-input label="Teléfono" name="newCustomerPhone" placeholder="0987654321" wire:model="newCustomerPhone" />
                <x-input label="Correo Electrónico" name="newCustomerEmail" type="email" placeholder="carlos@ejemplo.com" wire:model="newCustomerEmail" />
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-primary-200">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'quick-customer-modal')">
                    {{ __t('cancel', 'sales') }}
                </x-button>
                <x-button type="submit">
                    Guardar y Seleccionar
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL 6: ANULAR VENTA --}}
    <x-modal name="cancel-sale-modal" :title="__t('cancel_sale', 'sales')" :description="__t('cancel_sale_confirm', 'sales')">
        <form wire:submit.prevent="cancelSale" class="p-6 space-y-4">
            <x-input
                label="Motivo de la anulación (opcional)"
                name="cancelReason"
                placeholder="Ej. Devolución del cliente, error en cobro..."
                wire:model="cancelReason" />

            <div class="flex justify-end gap-2 pt-3 border-t border-primary-200">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'cancel-sale-modal')">
                    {{ __t('cancel', 'sales') }}
                </x-button>
                <x-button type="submit" variant="danger">
                    Confirmar Anulación
                </x-button>
            </div>
        </form>
    </x-modal>
</div>
