<x-app-layout>
    <x-slot name="header">
        <x-page-header title="UI Foundation" description="Reusable Blade components for the Velntra administrative dashboard.">
            <x-slot:actions>
                <x-button variant="secondary" x-on:click="$dispatch('open-modal', 'delete-demo')">Delete modal</x-button>
                <x-button x-on:click="$dispatch('open-modal', 'form-demo')">Create item</x-button>
            </x-slot:actions>
        </x-page-header>
    </x-slot>

    <div class="space-y-8">
        <x-card title="Buttons">
            <div class="flex flex-wrap gap-3">
                <x-button>Primary</x-button>
                <x-button variant="accent">Accent</x-button>
                <x-button variant="secondary">Secondary</x-button>
                <x-button variant="danger">Danger</x-button>
                <x-button variant="ghost">Ghost</x-button>
            </div>
        </x-card>

        <x-form-section title="Form controls" description="Prepared for Laravel validation, Laravel Collective output and Livewire bindings.">
            <div class="grid gap-4 md:grid-cols-2">
                <x-input label="Product name" name="name" placeholder="Wireless scanner" required wire:model.defer="name" />
                <x-select label="Category" name="category_id" wire:model.defer="category_id">
                    <option value="">Select category</option>
                    <option>Accessories</option>
                    <option>Hardware</option>
                </x-select>
                <x-textarea label="Description" name="description" placeholder="Short product notes" class="md:col-span-2" />
                <x-checkbox name="is_active" label="Available for sale" wire:model="is_active" />
            </div>
        </x-form-section>

        <x-card title="Badges and alerts">
            <div class="space-y-4">
                <div class="flex flex-wrap gap-2">
                    <x-badge>Draft</x-badge>
                    <x-badge variant="success">Active</x-badge>
                    <x-badge variant="warning">Low stock</x-badge>
                    <x-badge variant="danger">Disabled</x-badge>
                    <x-badge variant="info">Synced</x-badge>
                </div>
                <x-alert variant="success" title="Ready">Components are visual only and can be wired to Livewire later.</x-alert>
                <div class="flex flex-wrap gap-2">
                    <x-button variant="secondary" x-on:click="$dispatch('toast', { type: 'success', title: 'Saved', message: 'The record was saved successfully.' })">Success toast</x-button>
                    <x-button variant="secondary" x-on:click="$dispatch('toast', { type: 'warning', title: 'Low stock', message: 'Some products need inventory review.' })">Warning toast</x-button>
                    <x-button variant="secondary" x-on:click="$dispatch('toast', { type: 'danger', title: 'Action failed', message: 'Please review the form and try again.' })">Danger toast</x-button>
                </div>
            </div>
        </x-card>

        <div class="grid gap-4 md:grid-cols-3">
            <x-stat-card label="Revenue" value="$18,240" trend="+8.2%" variant="success" />
            <x-stat-card label="Products" value="842" trend="18 low stock" variant="warning" />
            <x-stat-card label="Customers" value="1,942" trend="+38" variant="info" />
        </div>

        <x-table loading-target="search">
            <x-slot:toolbar>
                <x-table-search placeholder="Search inventory..." wire:model.live.debounce.300ms="search" />
                <x-filters-panel active-count="2" clear-action="clearFilters">
                    <x-select label="Status" name="status" class="min-w-40">
                        <option>All statuses</option>
                        <option>Active</option>
                        <option>Low stock</option>
                    </x-select>
                    <x-select label="Category" name="category" class="min-w-40">
                        <option>All categories</option>
                        <option>Accessories</option>
                        <option>Hardware</option>
                    </x-select>
                    <x-select label="Stock" name="stock" class="min-w-40">
                        <option>Any stock</option>
                        <option>In stock</option>
                        <option>Out of stock</option>
                    </x-select>
                </x-filters-panel>
            </x-slot:toolbar>

            <x-slot:filters>
                <x-badge>All warehouses</x-badge>
                <x-badge variant="success">Active</x-badge>
                <x-badge variant="warning">Low stock</x-badge>
            </x-slot:filters>

            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">SKU</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">Actions</th>
                </tr>
            </x-slot:head>

            @foreach ([['Barcode scanner', 'SKU-1001', 24, 'Active'], ['Receipt printer', 'SKU-1002', 5, 'Low stock'], ['Cash drawer', 'SKU-1003', 0, 'Disabled']] as $item)
                <tr>
                    <td class="px-4 py-4 text-sm font-medium text-primary-950">{{ $item[0] }}</td>
                    <td class="px-4 py-4 text-sm text-primary-500">{{ $item[1] }}</td>
                    <td class="px-4 py-4 text-sm text-primary-700">{{ $item[2] }}</td>
                    <td class="px-4 py-4">
                        <x-badge :variant="$item[3] === 'Active' ? 'success' : ($item[3] === 'Low stock' ? 'warning' : 'danger')">{{ $item[3] }}</x-badge>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <x-row-actions delete-modal="delete-demo" />
                    </td>
                </tr>
            @endforeach

            <x-slot:mobile>
                @foreach ([['Barcode scanner', 'SKU-1001', 24, 'Active'], ['Receipt printer', 'SKU-1002', 5, 'Low stock'], ['Cash drawer', 'SKU-1003', 0, 'Disabled']] as $item)
                    <x-mobile-record-card
                        :title="$item[0]"
                        subtitle="{{ $item[1] }}"
                        :status="$item[3]"
                        :status-variant="$item[3] === 'Active' ? 'success' : ($item[3] === 'Low stock' ? 'warning' : 'danger')"
                    >
                        <x-slot:meta>
                            <div>
                                <dt class="text-primary-400">SKU</dt>
                                <dd class="font-medium text-primary-800">{{ $item[1] }}</dd>
                            </div>
                            <div>
                                <dt class="text-primary-400">Stock</dt>
                                <dd class="font-medium text-primary-800">{{ $item[2] }}</dd>
                            </div>
                        </x-slot:meta>
                        <x-slot:actions>
                            <x-button variant="secondary" size="sm">View</x-button>
                            <x-button variant="secondary" size="sm">Edit</x-button>
                            <x-button variant="danger" size="sm" x-on:click="$dispatch('open-modal', 'delete-demo')">Delete</x-button>
                        </x-slot:actions>
                    </x-mobile-record-card>
                @endforeach
            </x-slot:mobile>

            <x-slot:pagination>
                <x-pagination :current-page="2" :last-page="8" :from="11" :to="20" :total="76" />
            </x-slot:pagination>
        </x-table>

        <x-card title="States and loading">
            <div class="grid gap-4 lg:grid-cols-3">
                <x-state variant="forbidden" title="Access denied" description="You do not have permission to manage this resource." />
                <x-state variant="error" title="Something went wrong" description="The records could not be loaded. Try refreshing the page." />
                <x-skeleton type="card" />
            </div>
        </x-card>

        <x-crud-page title="CRUD page convention" description="Use this structure for index pages with actions, summaries, responsive tables and modals.">
            <x-slot:actions>
                <x-button variant="secondary">Import</x-button>
                <x-button x-on:click="$dispatch('open-modal', 'form-demo')">Create record</x-button>
            </x-slot:actions>
            <x-slot:summary>
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <x-stat-card label="Total records" value="842" trend="Demo data" variant="info" />
                    <x-stat-card label="Active" value="798" trend="94.7%" variant="success" />
                    <x-stat-card label="Draft" value="31" trend="Needs review" variant="warning" />
                    <x-stat-card label="Archived" value="13" trend="Hidden" />
                </div>
            </x-slot:summary>
            <x-slot:content>
                <x-state title="Drop the table component here" description="Keep index, table, mobile card, create/edit modal and confirm dialog together." />
            </x-slot:content>
        </x-crud-page>

        <x-card title="Empty state">
            <x-empty-state title="No products yet" description="Create the first product once the Inventory module is implemented.">
                <x-slot:action>
                    <x-button>Create product</x-button>
                </x-slot:action>
            </x-empty-state>
        </x-card>
    </div>

    <x-modal name="form-demo" title="Create product" description="Example modal prepared for Livewire create and edit forms.">
        <form wire:submit.prevent="save" class="space-y-5 p-6">
            <x-input label="Name" name="modal_name" placeholder="Product name" wire:model.defer="modalName" />
            <x-select label="Status" name="modal_status" wire:model.defer="modalStatus">
                <option>Active</option>
                <option>Draft</option>
            </x-select>
            <div class="flex justify-end gap-2 border-t border-primary-200 pt-5">
                <x-button variant="secondary" x-on:click="$dispatch('close-modal', 'form-demo')">Cancel</x-button>
                <x-button type="submit" wire:loading.attr="disabled">Save</x-button>
            </div>
        </form>
    </x-modal>

    <x-confirm-dialog name="delete-demo" title="Delete product" action="delete">
        This is only a visual confirmation pattern. No record will be deleted from the demo.
    </x-confirm-dialog>
</x-app-layout>
