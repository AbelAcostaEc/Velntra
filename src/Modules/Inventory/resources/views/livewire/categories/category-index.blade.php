<div>
    <x-crud-page :title="__t('categories_management', 'inventory')" :description="__t('categories_description', 'inventory')">
        <x-slot:actions>
            @can('create', \Modules\Inventory\Models\Category::class)
                <x-button
                    wire:click="openCreateModal"
                    x-on:click="$dispatch('open-modal', 'category-form')"
                    wire:loading.attr="disabled">
                    <svg class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __t('create_category', 'inventory') }}
                </x-button>
            @endcan
        </x-slot:actions>

        <x-slot:summary>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <x-stat-card :label="__t('total_categories', 'inventory')" value="{{ $totalCategories }}" :trend="__t('all_registered', 'inventory')" variant="info" />
                <x-stat-card :label="__t('active_categories', 'inventory')" value="{{ $activeCategories }}" :trend="__t('in_operation', 'inventory')" variant="success" />
                <x-stat-card :label="__t('inactive_categories', 'inventory')" value="{{ $inactiveCategories }}" :trend="__t('temporarily_disabled', 'inventory')" variant="warning" />
            </div>
        </x-slot:summary>

        <x-slot:content>
            <x-table loading-target="search, statusFilter, perPage">
                <x-slot:toolbar>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full">
                        <x-table-search :placeholder="__t('search_categories_placeholder', 'inventory')" wire:model.live.debounce.300ms="search" class="w-full sm:max-w-xs" />

                        <div class="flex flex-wrap items-center gap-2">
                            <select
                                wire:model.live="statusFilter"
                                class="h-10 rounded-xl border-primary-200 bg-white py-0 pl-3 pr-8 text-sm font-medium text-primary-700 shadow-sm focus:border-accent-500 focus:ring-accent-500"
                            >
                                <option value="">{{ __t('all_statuses', 'inventory') }}</option>
                                <option value="active">{{ __t('active', 'inventory') }}</option>
                                <option value="inactive">{{ __t('inactive', 'inventory') }}</option>
                            </select>

                            <x-per-page-select wire:model.live="perPage" />
                        </div>
                    </div>
                </x-slot:toolbar>

                <x-slot:head>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_code', 'inventory') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_name', 'inventory') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_description', 'inventory') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_status', 'inventory') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_actions', 'inventory') }}</th>
                    </tr>
                </x-slot:head>

                @forelse($categories as $category)
                    <tr class="hover:bg-primary-50/50 transition-colors">
                        <td class="px-4 py-4 text-sm font-mono font-medium text-primary-950">{{ $category->code }}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-primary-950">{{ $category->name }}</td>
                        <td class="px-4 py-4 text-sm text-primary-600 max-w-xs truncate">{{ $category->description ?: __t('no_description', 'inventory') }}</td>
                        <td class="px-4 py-4">
                            <x-badge :variant="$category->is_active ? 'success' : 'danger'">
                                {{ $category->is_active ? __t('active', 'inventory') : __t('inactive', 'inventory') }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('update', $category)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $category->id }})"
                                        x-on:click="$dispatch('open-modal', 'category-form')">
                                        {{ __t('edit', 'inventory') }}
                                    </x-button>
                                @endcan
                                @can('delete', $category)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $category->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-category')">
                                        {{ __t('delete', 'inventory') }}
                                    </x-button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <x-empty-state
                                :title="__t('no_categories_found', 'inventory')"
                                :description="__t('no_categories_description', 'inventory')" />
                        </td>
                    </tr>
                @endforelse

                <x-slot:mobile>
                    @foreach ($categories as $category)
                        <x-mobile-record-card
                            :title="$category->name"
                            :subtitle="$category->code">
                            <x-slot:status>
                                <x-badge :variant="$category->is_active ? 'success' : 'danger'">
                                    {{ $category->is_active ? __t('active', 'inventory') : __t('inactive', 'inventory') }}
                                </x-badge>
                            </x-slot:status>

                            <x-slot:actions>
                                @can('update', $category)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $category->id }})"
                                        x-on:click="$dispatch('open-modal', 'category-form')">
                                        {{ __t('edit', 'inventory') }}
                                    </x-button>
                                @endcan
                                @can('delete', $category)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $category->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-category')">
                                        {{ __t('delete', 'inventory') }}
                                    </x-button>
                                @endcan
                            </x-slot:actions>
                        </x-mobile-record-card>
                    @endforeach
                </x-slot:mobile>

                <x-slot:pagination>
                    <div class="px-4 py-3">
                        {{ $categories->links() }}
                    </div>
                </x-slot:pagination>
            </x-table>
        </x-slot:content>
    </x-crud-page>

    {{-- Modal Formulario Crear / Editar --}}
    <x-modal
        name="category-form"
        :title="$selectedCategoryId ? __t('edit_category', 'inventory') : __t('create_category', 'inventory')"
        :description="__t('form_description', 'inventory')"
        loading-target="openCreateModal, openEditModal">
        <form wire:submit.prevent="save" class="space-y-5 p-6">
            <x-input
                :label="__t('field_code', 'inventory')"
                name="code"
                :placeholder="__t('field_code_placeholder', 'inventory')"
                wire:model="code"
                required />

            <x-input
                :label="__t('field_name', 'inventory')"
                name="name"
                :placeholder="__t('field_name_placeholder', 'inventory')"
                wire:model="name"
                required />

            <div>
                <label class="block text-sm font-medium text-primary-700 mb-1.5">
                    {{ __t('field_description', 'inventory') }}
                </label>
                <textarea
                    wire:model="description"
                    rows="3"
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
                    id="is_active"
                    wire:model="is_active"
                    class="h-4 w-4 rounded border-primary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                />
                <label for="is_active" class="text-sm font-medium text-primary-700 cursor-pointer select-none">
                    {{ __t('field_is_active', 'inventory') }}
                </label>
            </div>

            <div class="flex justify-end gap-2 border-t border-primary-200 pt-5">
                <x-button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close-modal', 'category-form')">
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
        name="delete-category"
        :title="__t('delete_category_confirm_title', 'inventory')"
        :description="__t('delete_category_confirm_description', 'inventory', ['name' => $deletingCategoryName])"
        :confirm-label="__t('delete', 'inventory')"
        :cancel-label="__t('cancel', 'inventory')"
        action="delete"
        loading-target="openDeleteModal, delete" />
</div>
