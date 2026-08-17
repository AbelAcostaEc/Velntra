<div>
    <x-crud-page :title="__t('customers_management', 'customers')" :description="__t('customers_description', 'customers')">
        <x-slot:actions>
            @can('create', \Modules\Customers\Models\Customer::class)
                <x-button
                    wire:click="openCreateModal"
                    x-on:click="$dispatch('open-modal', 'customer-form')"
                    wire:loading.attr="disabled">
                    <svg class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __t('create_customer', 'customers') }}
                </x-button>
            @endcan
        </x-slot:actions>

        <x-slot:summary>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <x-stat-card :label="__t('total_customers', 'customers')" value="{{ $totalCustomers }}" :trend="__t('all_registered', 'customers')" variant="info" />
                <x-stat-card :label="__t('active_customers', 'customers')" value="{{ $activeCustomers }}" :trend="__t('in_operation', 'customers')" variant="success" />
                <x-stat-card :label="__t('inactive_customers', 'customers')" value="{{ $inactiveCustomers }}" :trend="__t('temporarily_disabled', 'customers')" variant="warning" />
            </div>
        </x-slot:summary>

        <x-slot:content>
            <x-table loading-target="search, perPage, statusFilter">
                <x-slot:toolbar>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:max-w-xl">
                            <x-table-search :placeholder="__t('search_placeholder', 'customers')" wire:model.live.debounce.300ms="search" class="w-full sm:max-w-sm" />
                            
                            <select
                                wire:model.live="statusFilter"
                                class="rounded-xl border border-primary-200 bg-white/80 px-3 py-2 text-sm text-primary-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                                <option value="">{{ __t('all_statuses', 'customers') }}</option>
                                <option value="active">{{ __t('active', 'customers') }}</option>
                                <option value="inactive">{{ __t('inactive', 'customers') }}</option>
                            </select>
                        </div>
                        <x-per-page-select wire:model.live="perPage" />
                    </div>
                </x-slot:toolbar>

                <x-slot:head>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_document', 'customers') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_name', 'customers') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_phone', 'customers') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_email', 'customers') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_status', 'customers') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_actions', 'customers') }}</th>
                    </tr>
                </x-slot:head>

                @forelse($customers as $customer)
                    <tr class="hover:bg-primary-50/50 transition-colors">
                        <td class="px-4 py-4 text-sm text-primary-700">
                            @if ($customer->document)
                                <span class="font-mono text-xs px-2 py-1 bg-primary-100/70 text-primary-800 rounded-md">
                                    {{ $customer->document }}
                                </span>
                            @else
                                <span class="text-xs text-primary-400 italic">{{ __t('no_document', 'customers') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm font-medium text-primary-950">
                            <div class="flex items-center gap-2">
                                <span>{{ $customer->name }}</span>
                                @if ($customer->isConsumidorFinal())
                                    <x-badge variant="info">
                                        {{ __t('final_consumer_badge', 'customers') }}
                                    </x-badge>
                                @endif
                            </div>
                            @if ($customer->address)
                                <div class="text-xs text-primary-400 truncate max-w-xs mt-0.5" title="{{ $customer->address }}">
                                    {{ $customer->address }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-primary-600">
                            {{ $customer->phone ?: '—' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-primary-600">
                            {{ $customer->email ?: '—' }}
                        </td>
                        <td class="px-4 py-4">
                            <button
                                type="button"
                                wire:click="toggleStatus({{ $customer->id }})"
                                class="cursor-pointer transition-transform active:scale-95">
                                <x-badge :variant="$customer->is_active ? 'success' : 'neutral'">
                                    {{ $customer->is_active ? __t('active', 'customers') : __t('inactive', 'customers') }}
                                </x-badge>
                            </button>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('update', $customer)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $customer->id }})"
                                        x-on:click="$dispatch('open-modal', 'customer-form')">
                                        {{ __t('edit', 'customers') }}
                                    </x-button>
                                @endcan

                                @can('delete', $customer)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $customer->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-customer')">
                                        {{ __t('delete', 'customers') }}
                                    </x-button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-empty-state
                                :title="__t('no_customers_title', 'customers')"
                                :description="__t('no_customers_description', 'customers')" />
                        </td>
                    </tr>
                @endforelse

                <x-slot:mobile>
                    @foreach ($customers as $customer)
                        <x-mobile-record-card
                            :title="$customer->name"
                            :subtitle="$customer->document ?: $customer->email">
                            <x-slot:status>
                                <x-badge :variant="$customer->is_active ? 'success' : 'neutral'">
                                    {{ $customer->is_active ? __t('active', 'customers') : __t('inactive', 'customers') }}
                                </x-badge>
                                @if ($customer->isConsumidorFinal())
                                    <x-badge variant="info">
                                        {{ __t('final_consumer_badge', 'customers') }}
                                    </x-badge>
                                @endif
                            </x-slot:status>

                            <x-slot:actions>
                                @can('update', $customer)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $customer->id }})"
                                        x-on:click="$dispatch('open-modal', 'customer-form')">
                                        {{ __t('edit', 'customers') }}
                                    </x-button>
                                @endcan

                                @can('delete', $customer)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $customer->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-customer')">
                                        {{ __t('delete', 'customers') }}
                                    </x-button>
                                @endcan
                            </x-slot:actions>
                        </x-mobile-record-card>
                    @endforeach
                </x-slot:mobile>

                <x-slot:pagination>
                    <div class="px-4 py-3">
                        {{ $customers->links() }}
                    </div>
                </x-slot:pagination>
            </x-table>
        </x-slot:content>
    </x-crud-page>

    {{-- Modal Formulario Crear / Editar --}}
    <x-modal
        name="customer-form"
        :title="$selectedCustomerId ? __t('edit_customer', 'customers') : __t('create_customer', 'customers')"
        :description="__t('form_description', 'customers')"
        loading-target="openCreateModal, openEditModal">
        <form wire:submit.prevent="save" class="space-y-5 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    :label="__t('field_name', 'customers')"
                    name="name"
                    :placeholder="__t('field_name_placeholder', 'customers')"
                    wire:model="name"
                    required />

                <x-input
                    :label="__t('field_document', 'customers')"
                    name="document"
                    :placeholder="__t('field_document_placeholder', 'customers')"
                    wire:model="document" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    :label="__t('field_phone', 'customers')"
                    name="phone"
                    :placeholder="__t('field_phone_placeholder', 'customers')"
                    wire:model="phone" />

                <x-input
                    :label="__t('field_email', 'customers')"
                    name="email"
                    type="email"
                    :placeholder="__t('field_email_placeholder', 'customers')"
                    wire:model="email" />
            </div>

            <x-input
                :label="__t('field_address', 'customers')"
                name="address"
                :placeholder="__t('field_address_placeholder', 'customers')"
                wire:model="address" />

            <div class="flex items-center gap-2 pt-2">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        wire:model="is_active"
                        class="h-4 w-4 rounded border-primary-300 text-brand-600 focus:ring-brand-500 cursor-pointer"
                    />
                    <span class="text-sm font-medium text-primary-700">
                        {{ __t('field_is_active', 'customers') }}
                    </span>
                </label>
                <span class="text-xs text-primary-500">({{ __t('field_is_active_description', 'customers') }})</span>
            </div>

            <div class="flex justify-end gap-2 border-t border-primary-200 pt-5">
                <x-button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close-modal', 'customer-form')">
                    {{ __t('cancel', 'customers') }}
                </x-button>

                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __t('save', 'customers') }}</span>
                    <span wire:loading>{{ __t('saving', 'customers') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Confirmación de Eliminación --}}
    <x-confirm-dialog
        name="delete-customer"
        :title="__t('delete_customer_title', 'customers')"
        :description="__t('delete_customer_description', 'customers')"
        :confirm-label="__t('delete', 'customers')"
        :cancel-label="__t('cancel', 'customers')"
        action="delete"
        loading-target="openDeleteModal, delete" />
</div>
