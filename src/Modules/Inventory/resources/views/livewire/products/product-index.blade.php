<div>
    <x-crud-page :title="__t('products_management', 'inventory')" :description="__t('products_description', 'inventory')">
        <x-slot:actions>
            @can('create', \Modules\Inventory\Models\Product::class)
                <x-button
                    wire:click="openCreateModal"
                    x-on:click="$dispatch('open-modal', 'product-form')"
                    wire:loading.attr="disabled">
                    <svg class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __t('create_product', 'inventory') }}
                </x-button>
            @endcan
        </x-slot:actions>

        <x-slot:summary>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <x-stat-card :label="__t('total_products', 'inventory')" value="{{ $totalProducts }}" :trend="__t('available_in_catalog', 'inventory')" variant="info" />
                <x-stat-card :label="__t('low_stock_products', 'inventory')" value="{{ $lowStockProducts }}" :trend="__t('needs_replenishment', 'inventory')" variant="warning" />
                <x-stat-card :label="__t('active_products', 'inventory')" value="{{ $activeProducts }}" :trend="__t('in_operation', 'inventory')" variant="success" />
            </div>
        </x-slot:summary>

        <x-slot:content>
            <x-table loading-target="search, categoryFilter, statusFilter">
                <x-slot:toolbar>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full">
                        <x-table-search :placeholder="__t('search_products_placeholder', 'inventory')" wire:model.live.debounce.300ms="search" class="w-full sm:max-w-xs" />

                        <div class="flex flex-wrap items-center gap-2">
                            <select
                                wire:model.live="categoryFilter"
                                class="h-10 rounded-xl border-primary-200 bg-white py-0 pl-3 pr-8 text-sm font-medium text-primary-700 shadow-sm focus:border-accent-500 focus:ring-accent-500"
                            >
                                <option value="">{{ __t('filter_by_category', 'inventory') }}</option>
                                @foreach($allCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>

                            <select
                                wire:model.live="statusFilter"
                                class="h-10 rounded-xl border-primary-200 bg-white py-0 pl-3 pr-8 text-sm font-medium text-primary-700 shadow-sm focus:border-accent-500 focus:ring-accent-500"
                            >
                                <option value="">{{ __t('all_statuses', 'inventory') }}</option>
                                <option value="active">{{ __t('active', 'inventory') }}</option>
                                <option value="inactive">{{ __t('inactive', 'inventory') }}</option>
                            </select>
                        </div>
                    </div>
                </x-slot:toolbar>

                <x-slot:head>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_sku', 'inventory') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_product', 'inventory') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_categories', 'inventory') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_price_cost', 'inventory') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_stock', 'inventory') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_status', 'inventory') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_actions', 'inventory') }}</th>
                    </tr>
                </x-slot:head>

                @forelse($products as $product)
                    <tr class="hover:bg-primary-50/50 transition-colors">
                        <td class="px-4 py-4 text-sm font-mono font-medium text-primary-950">
                            <span class="inline-flex items-center rounded-lg bg-primary-100 px-2 py-0.5 text-xs font-mono font-semibold text-primary-700">
                                {{ $product->sku }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm font-semibold text-primary-950">
                            <div>{{ $product->name }}</div>
                            @if($product->barcode)
                                <div class="text-xs font-mono font-normal text-primary-500">{{ $product->barcode }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($product->categories as $c)
                                    <x-badge variant="info">
                                        {{ $c->name }}
                                    </x-badge>
                                @empty
                                    <span class="text-xs text-primary-400 italic">{{ __t('no_categories_assigned', 'inventory') }}</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <div class="font-semibold text-primary-950">${{ number_format((float)$product->price, 2) }}</div>
                            <div class="text-xs text-primary-500">${{ number_format((float)$product->cost, 2) }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <div class="flex items-center gap-1.5">
                                <span class="font-medium text-primary-950">{{ $product->stock }}</span>
                                @if($product->stock <= $product->minimum_stock)
                                    <x-badge variant="warning">
                                        {{ __t('low_stock_badge', 'inventory') }}
                                    </x-badge>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <x-badge :variant="$product->is_active ? 'success' : 'danger'">
                                {{ $product->is_active ? __t('active', 'inventory') : __t('inactive', 'inventory') }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('update', $product)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $product->id }})"
                                        x-on:click="$dispatch('open-modal', 'product-form')">
                                        {{ __t('edit', 'inventory') }}
                                    </x-button>
                                @endcan
                                @can('delete', $product)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $product->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-product')">
                                        {{ __t('delete', 'inventory') }}
                                    </x-button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-empty-state
                                :title="__t('no_products_found', 'inventory')"
                                :description="__t('no_products_description', 'inventory')" />
                        </td>
                    </tr>
                @endforelse

                <x-slot:mobile>
                    @foreach ($products as $product)
                        <x-mobile-record-card
                            :title="$product->name"
                            :subtitle="'SKU: ' . $product->sku . ($product->barcode ? ' | Barcode: ' . $product->barcode : '')">
                            <x-slot:status>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <x-badge :variant="$product->is_active ? 'success' : 'danger'">
                                        {{ $product->is_active ? __t('active', 'inventory') : __t('inactive', 'inventory') }}
                                    </x-badge>
                                    @if($product->stock <= $product->minimum_stock)
                                        <x-badge variant="warning">
                                            {{ __t('low_stock_badge', 'inventory') }}
                                        </x-badge>
                                    @endif
                                </div>
                            </x-slot:status>

                            <x-slot:actions>
                                @can('update', $product)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $product->id }})"
                                        x-on:click="$dispatch('open-modal', 'product-form')">
                                        {{ __t('edit', 'inventory') }}
                                    </x-button>
                                @endcan
                                @can('delete', $product)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $product->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-product')">
                                        {{ __t('delete', 'inventory') }}
                                    </x-button>
                                @endcan
                            </x-slot:actions>
                        </x-mobile-record-card>
                    @endforeach
                </x-slot:mobile>

                <x-slot:pagination>
                    <div class="px-4 py-3">
                        {{ $products->links() }}
                    </div>
                </x-slot:pagination>
            </x-table>
        </x-slot:content>
    </x-crud-page>

    {{-- Modal Formulario Crear / Editar Producto --}}
    <x-modal
        name="product-form"
        :title="$selectedProductId ? __t('edit_product', 'inventory') : __t('create_product', 'inventory')"
        :description="__t('product_form_description', 'inventory')"
        max-width="2xl"
        loading-target="openCreateModal, openEditModal">
        <form wire:submit.prevent="save" class="space-y-5 p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-input
                    :label="__t('field_sku', 'inventory')"
                    name="sku"
                    :placeholder="__t('field_sku_placeholder', 'inventory')"
                    wire:model="sku"
                    required />

                <x-input
                    :label="__t('field_barcode', 'inventory')"
                    name="barcode"
                    :placeholder="__t('field_barcode_placeholder', 'inventory')"
                    wire:model="barcode" />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <x-input
                        :label="__t('field_product_name', 'inventory')"
                        name="name"
                        :placeholder="__t('field_product_name_placeholder', 'inventory')"
                        wire:model="name"
                        required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-700 mb-1.5">
                        {{ __t('field_type', 'inventory') }}
                        <span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <select
                        wire:model="type"
                        class="h-10 w-full rounded-xl border-primary-200 bg-white py-0 pl-3 pr-8 text-sm font-medium text-primary-700 shadow-sm focus:border-accent-500 focus:ring-accent-500"
                    >
                        <option value="simple">{{ __t('type_simple', 'inventory') }}</option>
                        <option value="composite">{{ __t('type_composite', 'inventory') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <x-input
                        :label="__t('field_cost', 'inventory')"
                        name="cost"
                        type="number"
                        step="0.01"
                        min="0"
                        wire:model="cost"
                        required />
                </div>

                <div>
                    <x-input
                        :label="__t('field_price', 'inventory')"
                        name="price"
                        type="number"
                        step="0.01"
                        min="0"
                        wire:model="price"
                        required />
                </div>

                <div>
                    <x-input
                        :label="__t('field_stock', 'inventory')"
                        name="stock"
                        type="number"
                        min="0"
                        wire:model="stock"
                        required />
                </div>

                <div>
                    <x-input
                        :label="__t('field_minimum_stock', 'inventory')"
                        name="minimum_stock"
                        type="number"
                        min="0"
                        wire:model="minimum_stock"
                        required />
                </div>
            </div>

            {{-- Categories (Checkboxes - Multiple Selection) --}}
            <div>
                <label class="block text-sm font-medium text-primary-700 mb-1.5">
                    {{ __t('field_categories', 'inventory') }}
                </label>
                <p class="text-xs text-primary-500 mb-2.5">{{ __t('field_categories_help', 'inventory') }}</p>

                @if($allCategories->isNotEmpty())
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 max-h-36 overflow-y-auto p-3 rounded-xl border border-primary-200 bg-primary-50/30">
                        @foreach ($allCategories as $cat)
                            <label class="flex items-center gap-2 cursor-pointer select-none group">
                                <input
                                    type="checkbox"
                                    value="{{ $cat->id }}"
                                    wire:model="selectedCategories"
                                    class="h-4 w-4 rounded border-primary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                                />
                                <span class="text-sm text-primary-700 group-hover:text-primary-900 transition-colors truncate">
                                    {{ $cat->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-primary-400 italic p-3 bg-primary-50/50 rounded-xl border border-primary-100">
                        {{ __t('no_categories_available', 'inventory') }}
                    </p>
                @endif
                @error('selectedCategories')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-primary-700 mb-1.5">
                    {{ __t('field_description', 'inventory') }}
                </label>
                <textarea
                    wire:model="description"
                    rows="2"
                    class="w-full rounded-xl border-primary-200 bg-white p-3 text-sm text-primary-900 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    placeholder="{{ __t('field_description_placeholder', 'inventory') }}"
                ></textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    id="product_is_active"
                    wire:model="is_active"
                    class="h-4 w-4 rounded border-primary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                />
                <label for="product_is_active" class="text-sm font-medium text-primary-700 cursor-pointer select-none">
                    {{ __t('field_is_active', 'inventory') }}
                </label>
            </div>

            <div class="flex justify-end gap-2 border-t border-primary-200 pt-5">
                <x-button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close-modal', 'product-form')">
                    {{ __t('cancel', 'inventory') }}
                </x-button>

                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __t('save', 'inventory') }}</span>
                    <span wire:loading>{{ __t('saving', 'inventory') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Confirmación de Eliminación --}}
    <x-confirm-dialog
        name="delete-product"
        :title="__t('delete_product_confirm_title', 'inventory')"
        :description="__t('delete_product_confirm_description', 'inventory', ['name' => $deletingProductName])"
        :confirm-label="__t('delete', 'inventory')"
        :cancel-label="__t('cancel', 'inventory')"
        action="delete"
        loading-target="openDeleteModal, delete" />
</div>
