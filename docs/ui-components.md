# Velntra UI Components

Sprint 02 defines a reusable Blade UI foundation for the administrative dashboard. Components live in `src/resources/views/components` and use Tailwind CSS, Alpine.js and Livewire-friendly attributes.

## Design Tokens

The palette is centralized in two places:

- `src/resources/css/app.css`: CSS variables for RGB values.
- `src/tailwind.config.js`: Tailwind color names mapped to those variables.

Use `primary-*` for the main Slate palette and `accent-*` or `secondary-*` for the Emerald accent. To rebrand later, update the variables in `app.css`.

## Layout

```blade
<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Products" description="Manage sellable inventory.">
            <x-slot:actions>
                <x-button>Create product</x-button>
            </x-slot:actions>
        </x-page-header>
    </x-slot>

    Content here.
</x-app-layout>
```

The desktop sidebar can collapse to icon-only mode. The preference is stored in `localStorage` with the `velntra.sidebarCollapsed` key, so Livewire updates keep the selected state.

## Guest Layout

Authentication screens use `layouts.guest`. It is designed for Breeze and Livewire Volt pages such as login, registration, password reset, email verification and password confirmation.

```php
new #[Layout('layouts.guest')] class extends Component
{
    //
};
```

The Breeze form components `x-text-input`, `x-input-label`, `x-input-error`, `x-primary-button`, `x-secondary-button`, `x-danger-button` and `x-auth-session-status` are styled to match the Velntra dashboard.

## Language Selector

The topbar includes a visual language selector with English and Spanish.

```blade
<x-language-select />
```

The selected value is stored locally with the `velntra.locale` key. It is ready to connect later to Laravel locale handling.

## Forms

Inputs support labels, validation messages, placeholders, disabled, required and Livewire attributes.

```blade
<x-input label="Name" name="name" placeholder="Product name" required wire:model.defer="name" />

<x-select label="Category" name="category_id" wire:model.defer="category_id">
    <option value="">Select category</option>
</x-select>

<x-textarea label="Description" name="description" wire:model.defer="description" />

<x-checkbox name="is_active" label="Available for sale" wire:model="is_active" />
```

Laravel Collective can render fields inside a `Form Section` when a custom helper is needed.

```blade
<x-form-section title="Product data" description="Basic product information.">
    {!! Form::label('name', 'Name', ['class' => 'mb-1.5 block text-sm font-medium text-primary-700']) !!}
    {!! Form::text('name', null, ['class' => 'block w-full rounded-xl border-primary-200 text-sm focus:border-accent-500 focus:ring-accent-500']) !!}
</x-form-section>
```

## Buttons, Badges and Alerts

```blade
<x-button>Create</x-button>
<x-button variant="secondary">Cancel</x-button>
<x-button variant="danger" wire:click="delete">Delete</x-button>

<x-badge variant="success">Active</x-badge>
<x-alert variant="warning" title="Low stock">Review this product soon.</x-alert>
```

## Toast Notifications

`x-toast` is included once in the app layout. Dispatch a browser event from Alpine or Livewire to show a modern autohide notification.

```blade
<x-button
    x-on:click="$dispatch('toast', {
        type: 'success',
        title: 'Saved',
        message: 'The record was saved successfully.'
    })"
>
    Show toast
</x-button>
```

From Livewire, dispatch the same browser event:

```php
$this->dispatch('toast', type: 'success', title: 'Saved', message: 'The record was saved successfully.');
```

## Cards

```blade
<x-card title="Inventory" description="Current stock summary.">
    Card content.
</x-card>

<x-stat-card label="Products" value="842" trend="18 low stock" variant="warning" />
```

## Tables and Mobile Cards

Use `x-table` for the shared desktop/mobile pattern. Desktop rows go in the default slot. Mobile cards go in the `mobile` slot.

```blade
<x-table loading-target="search">
    <x-slot:toolbar>
        <x-table-search wire:model.live.debounce.300ms="search" />
        <x-filters-panel active-count="2" clear-action="clearFilters">
            <x-select label="Status" name="status" wire:model.live="status">
                <option value="">All statuses</option>
                <option value="active">Active</option>
            </x-select>
        </x-filters-panel>
    </x-slot:toolbar>

    <x-slot:head>
        <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">Product</th>
            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">Actions</th>
        </tr>
    </x-slot:head>

    <tr>
        <td class="px-4 py-4 text-sm text-primary-950">Barcode scanner</td>
        <td class="px-4 py-4 text-right"><x-row-actions delete-modal="delete-product" /></td>
    </tr>

    <x-slot:mobile>
        <x-mobile-record-card title="Barcode scanner" subtitle="SKU-1001" status="Active" status-variant="success">
            <x-slot:meta>
                <div>
                    <dt class="text-primary-400">Stock</dt>
                    <dd class="font-medium text-primary-800">24</dd>
                </div>
            </x-slot:meta>
            <x-slot:actions>
                <x-button variant="secondary" size="sm">Edit</x-button>
                <x-button variant="danger" size="sm" x-on:click="$dispatch('open-modal', 'delete-product')">Delete</x-button>
            </x-slot:actions>
        </x-mobile-record-card>
    </x-slot:mobile>

    <x-slot:pagination>
        <x-pagination :paginator="$products" livewire />
    </x-slot:pagination>
</x-table>
```

Use `livewire` when the table is inside a Livewire component. The paginator will call `previousPage`, `nextPage` and `gotoPage` without a full page reload.

For non-paginator demos or custom adapters, pass simple values:

```blade
<x-pagination :current-page="2" :last-page="8" :from="11" :to="20" :total="76" />
```

## Table Filters

`x-filters-panel` shows filters inline on desktop and as a collapsible panel on mobile.

```blade
<x-filters-panel active-count="1" clear-action="clearFilters">
    <x-select label="Status" name="status" wire:model.live="status">
        <option value="">All statuses</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </x-select>

    <x-select label="Category" name="category_id" wire:model.live="category_id">
        <option value="">All categories</option>
    </x-select>
</x-filters-panel>
```

## Row Actions

Use `x-row-actions` for consistent View, Edit and Delete actions in desktop tables.

```blade
<x-row-actions
    view-href="{{ route('products.show', $product) }}"
    edit-href="{{ route('products.edit', $product) }}"
    delete-modal="delete-product"
/>
```

Custom actions can be passed through the `items` slot.

```blade
<x-row-actions>
    <x-slot:items>
        <button wire:click="duplicate({{ $product->id }})" class="block w-full px-4 py-2 text-left text-sm text-primary-600 hover:bg-primary-50">
            Duplicate
        </button>
    </x-slot:items>
</x-row-actions>
```

## Modals and Confirm Dialogs

The modal listens for Alpine events and accepts Livewire loading attributes inside forms or actions.

```blade
<x-button x-on:click="$dispatch('open-modal', 'product-form')">Open</x-button>

<x-modal name="product-form" title="Create product" description="Add a new inventory item.">
    <form wire:submit.prevent="save" class="space-y-5 p-6">
        <x-input label="Name" name="name" wire:model.defer="name" />
        <div class="flex justify-end gap-2">
            <x-button variant="secondary" x-on:click="$dispatch('close-modal', 'product-form')">Cancel</x-button>
            <x-button type="submit" wire:loading.attr="disabled">Save</x-button>
        </div>
    </form>
</x-modal>
```

For delete confirmations, prefer `x-confirm-dialog`.

```blade
<x-button variant="danger" x-on:click="$dispatch('open-modal', 'delete-product')">Delete</x-button>

<x-confirm-dialog name="delete-product" title="Delete product" action="delete">
    This action cannot be undone.
</x-confirm-dialog>
```

## Standard States

Use `x-state` for reusable empty, forbidden, error and success states.

```blade
<x-state
    variant="forbidden"
    title="Access denied"
    description="You do not have permission to manage this resource."
/>

<x-state
    variant="error"
    title="Something went wrong"
    description="The records could not be loaded. Try refreshing the page."
/>
```

Use `x-skeleton` while Livewire loads content.

```blade
<div wire:loading>
    <x-skeleton type="table" :rows="6" />
</div>

<div wire:loading.remove>
    Loaded content.
</div>
```

## Empty States

```blade
<x-empty-state title="No products yet" description="Create the first product once the module is ready.">
    <x-slot:action>
        <x-button>Create product</x-button>
    </x-slot:action>
</x-empty-state>
```

## CRUD Page Convention

Use this convention for CRUD screens:

```text
Modules/Inventory/resources/views/livewire/categories/index.blade.php
Modules/Inventory/resources/views/livewire/categories/partials/table.blade.php
Modules/Inventory/resources/views/livewire/categories/partials/mobile-card.blade.php
Modules/Inventory/resources/views/livewire/categories/partials/form-modal.blade.php
Modules/Inventory/resources/views/livewire/categories/partials/delete-dialog.blade.php
```

The page shell should follow this pattern:

```blade
<x-crud-page title="Categories" description="Organize products for reporting and search.">
    <x-slot:actions>
        <x-button x-on:click="$dispatch('open-modal', 'category-form')">Create category</x-button>
    </x-slot:actions>

    <x-slot:summary>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Total" value="{{ $total }}" />
        </div>
    </x-slot:summary>

    <x-slot:content>
        @include('inventory::livewire.categories.partials.table')
    </x-slot:content>

    <x-slot:modals>
        @include('inventory::livewire.categories.partials.form-modal')
        @include('inventory::livewire.categories.partials.delete-dialog')
    </x-slot:modals>
</x-crud-page>
```

## Styleguide

The visual guide is available at:

```text
/dashboard/styleguide
```
