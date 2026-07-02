# Velntra - Blade Components UI Kit

Sistema: **Velntra v1.0**  
Stack: **Laravel + Blade + Livewire + Tailwind CSS + Alpine.js**  
Objetivo: crear una interfaz moderna, limpia y reutilizable para el sistema POS e inventario.

---

## Principios de diseño

Velntra debe verse como un sistema profesional, moderno y simple.

### Estilo visual

- Diseño limpio y administrativo.
- Bordes redondeados: `rounded-xl` o `rounded-2xl`.
- Sombras suaves: `shadow-sm`, `shadow-md`.
- Colores sobrios: slate, zinc, emerald, blue, amber, red.
- Espaciado consistente: `p-4`, `p-6`, `gap-4`.
- Formularios claros y fáciles de usar.
- Tablas limpias, con buen espacio y acciones visibles.

### Paleta recomendada

```txt
Primary: emerald
Neutral: slate
Info: blue
Warning: amber
Danger: red
Success: emerald
Background: slate-50
Cards: white
Text: slate-900
Muted text: slate-500
Border: slate-200
```

---

# Componentes base

## 1. Button

Archivo sugerido:

```txt
resources/views/components/ui/button.blade.php
```

### Props

```php
variant="primary|secondary|danger|success|warning|ghost"
size="sm|md|lg"
type="button|submit|reset"
```

### Diseño base

```blade
<button {{ $attributes->merge([
    'type' => $type ?? 'button',
    'class' => 'inline-flex items-center justify-center gap-2 rounded-xl font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none'
]) }}>
    {{ $slot }}
</button>
```

### Clases por variante

```txt
primary: bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500
secondary: bg-slate-900 text-white hover:bg-slate-800 focus:ring-slate-500
danger: bg-red-600 text-white hover:bg-red-700 focus:ring-red-500
success: bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500
warning: bg-amber-500 text-white hover:bg-amber-600 focus:ring-amber-400
ghost: bg-transparent text-slate-700 hover:bg-slate-100 focus:ring-slate-300
```

### Clases por tamaño

```txt
sm: px-3 py-2 text-sm
md: px-4 py-2.5 text-sm
lg: px-5 py-3 text-base
```

### Uso

```blade
<x-ui.button variant="primary" type="submit">
    Save Product
</x-ui.button>
```

---

## 2. Input

Archivo sugerido:

```txt
resources/views/components/ui/input.blade.php
```

### Props

```php
label="Product name"
name="name"
type="text"
placeholder="Enter product name"
```

### Diseño base

```txt
block w-full rounded-xl border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm
placeholder:text-slate-400
focus:border-emerald-500 focus:ring-emerald-500
```

### Uso

```blade
<x-ui.input
    label="Product Name"
    name="name"
    placeholder="Example: Coca Cola 1L"
/>
```

---

## 3. Select

Archivo sugerido:

```txt
resources/views/components/ui/select.blade.php
```

### Props

```php
label="Category"
name="category_id"
```

### Diseño base

```txt
block w-full rounded-xl border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm
focus:border-emerald-500 focus:ring-emerald-500
```

### Uso

```blade
<x-ui.select label="Category" name="category_id">
    <option value="">Select a category</option>
    <option value="1">Beverages</option>
</x-ui.select>
```

---

## 4. Textarea

Archivo sugerido:

```txt
resources/views/components/ui/textarea.blade.php
```

### Diseño base

```txt
block w-full rounded-xl border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm
placeholder:text-slate-400
focus:border-emerald-500 focus:ring-emerald-500
```

### Uso

```blade
<x-ui.textarea
    label="Description"
    name="description"
    rows="4"
/>
```

---

## 5. Card

Archivo sugerido:

```txt
resources/views/components/ui/card.blade.php
```

### Diseño base

```txt
rounded-2xl border border-slate-200 bg-white shadow-sm
```

### Estructura sugerida

```blade
<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm']) }}>
    @isset($header)
        <div class="border-b border-slate-200 px-6 py-4">
            {{ $header }}
        </div>
    @endisset

    <div class="p-6">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 rounded-b-2xl">
            {{ $footer }}
        </div>
    @endisset
</div>
```

### Uso

```blade
<x-ui.card>
    <x-slot:header>
        <h2 class="text-lg font-semibold text-slate-900">Products</h2>
    </x-slot:header>

    Content here
</x-ui.card>
```

---

## 6. Table

Archivo sugerido:

```txt
resources/views/components/ui/table.blade.php
```

### Diseño base

```txt
overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
```

### Estilo recomendado

```txt
Header: bg-slate-50 text-slate-600 uppercase tracking-wide text-xs
Rows: hover:bg-slate-50 transition
Cells: px-6 py-4 text-sm
```

### Uso

```blade
<x-ui.table>
    <x-slot:head>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
        </tr>
    </x-slot:head>

    <tr>
        <td>Coca Cola 1L</td>
        <td>$1.50</td>
        <td><x-ui.badge variant="success">Active</x-ui.badge></td>
        <td class="text-right">Edit</td>
    </tr>
</x-ui.table>
```

---

## 7. Badge

Archivo sugerido:

```txt
resources/views/components/ui/badge.blade.php
```

### Props

```php
variant="success|danger|warning|info|neutral"
```

### Clases por variante

```txt
success: bg-emerald-50 text-emerald-700 ring-emerald-600/20
danger: bg-red-50 text-red-700 ring-red-600/20
warning: bg-amber-50 text-amber-700 ring-amber-600/20
info: bg-blue-50 text-blue-700 ring-blue-600/20
neutral: bg-slate-100 text-slate-700 ring-slate-600/20
```

### Diseño base

```txt
inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset
```

### Uso

```blade
<x-ui.badge variant="success">Active</x-ui.badge>
```

---

## 8. Alert

Archivo sugerido:

```txt
resources/views/components/ui/alert.blade.php
```

### Props

```php
variant="success|danger|warning|info"
title="Success"
```

### Diseño base

```txt
rounded-2xl border p-4 text-sm
```

### Uso

```blade
<x-ui.alert variant="success" title="Saved successfully">
    The product was created successfully.
</x-ui.alert>
```

---

## 9. Modal

Archivo sugerido:

```txt
resources/views/components/ui/modal.blade.php
```

### Stack

Usar Alpine.js para abrir y cerrar.

### Diseño recomendado

```txt
Overlay: fixed inset-0 bg-slate-900/50 backdrop-blur-sm
Modal: rounded-2xl bg-white shadow-xl max-w-lg w-full
Header: px-6 py-4 border-b
Body: p-6
Footer: px-6 py-4 border-t bg-slate-50 rounded-b-2xl
```

### Uso

```blade
<x-ui.modal name="deleteProductModal">
    <x-slot:title>Delete Product</x-slot:title>

    Are you sure you want to delete this product?

    <x-slot:footer>
        <x-ui.button variant="ghost">Cancel</x-ui.button>
        <x-ui.button variant="danger">Delete</x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

---

## 10. Dropdown

Archivo sugerido:

```txt
resources/views/components/ui/dropdown.blade.php
```

### Diseño recomendado

```txt
rounded-xl border border-slate-200 bg-white shadow-lg p-2 min-w-48
```

### Uso

```blade
<x-ui.dropdown>
    <x-slot:trigger>
        <x-ui.button variant="ghost">Actions</x-ui.button>
    </x-slot:trigger>

    <x-ui.dropdown-item>Edit</x-ui.dropdown-item>
    <x-ui.dropdown-item variant="danger">Delete</x-ui.dropdown-item>
</x-ui.dropdown>
```

---

# Componentes de layout

## 11. App Layout

Archivo sugerido:

```txt
resources/views/layouts/app.blade.php
```

### Diseño

```txt
Background: bg-slate-50
Sidebar fixed on desktop
Topbar sticky
Content max width with padding
```

### Estructura

```txt
Sidebar + Topbar + Main content
```

---

## 12. Sidebar

Archivo sugerido:

```txt
resources/views/components/layout/sidebar.blade.php
```

### Secciones MVP

```txt
Dashboard
Administration
Inventory
Customers
Sales
Reports
Settings
```

### Diseño recomendado

```txt
w-72 bg-slate-950 text-slate-300
Active item: bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
Normal item: hover:bg-white/5 hover:text-white
```

### Uso interno

```blade
<x-layout.sidebar-link route="dashboard.index" icon="home">
    Dashboard
</x-layout.sidebar-link>
```

---

## 13. Sidebar Link

Archivo sugerido:

```txt
resources/views/components/layout/sidebar-link.blade.php
```

### Props

```php
route="products.index"
icon="box"
active="true|false"
```

### Diseño base

```txt
flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
```

---

## 14. Topbar

Archivo sugerido:

```txt
resources/views/components/layout/topbar.blade.php
```

### Contenido

```txt
Page title
Search opcional
User menu
```

### Diseño recomendado

```txt
sticky top-0 z-30 border-b border-slate-200 bg-white/80 backdrop-blur-xl
```

---

## 15. Page Header

Archivo sugerido:

```txt
resources/views/components/layout/page-header.blade.php
```

### Props

```php
title="Products"
description="Manage your store products"
```

### Uso

```blade
<x-layout.page-header
    title="Products"
    description="Manage your store products and prices."
>
    <x-ui.button variant="primary">Create Product</x-ui.button>
</x-layout.page-header>
```

---

# Componentes para formularios

## 16. Form Section

Archivo sugerido:

```txt
resources/views/components/ui/form-section.blade.php
```

### Diseño

```txt
grid grid-cols-1 gap-6 lg:grid-cols-3
```

### Uso

```blade
<x-ui.form-section
    title="Product Information"
    description="Basic information used in inventory and sales."
>
    <div class="lg:col-span-2 space-y-4">
        <x-ui.input label="Name" name="name" />
        <x-ui.input label="Price" name="price" type="number" />
    </div>
</x-ui.form-section>
```

---

## 17. Error Message

Archivo sugerido:

```txt
resources/views/components/ui/error.blade.php
```

### Diseño base

```txt
mt-1 text-sm text-red-600
```

### Uso

```blade
<x-ui.error name="name" />
```

---

## 18. Empty State

Archivo sugerido:

```txt
resources/views/components/ui/empty-state.blade.php
```

### Props

```php
title="No products found"
description="Create your first product to start selling."
```

### Diseño recomendado

```txt
rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center
```

### Uso

```blade
<x-ui.empty-state
    title="No products found"
    description="Create your first product to start managing inventory."
>
    <x-ui.button variant="primary">Create Product</x-ui.button>
</x-ui.empty-state>
```

---

# Componentes para dashboard

## 19. Stat Card

Archivo sugerido:

```txt
resources/views/components/dashboard/stat-card.blade.php
```

### Props

```php
label="Total Sales"
value="$1,250.00"
trend="+12%"
variant="success|danger|neutral"
```

### Diseño recomendado

```txt
rounded-2xl border border-slate-200 bg-white p-6 shadow-sm
```

### Uso

```blade
<x-dashboard.stat-card
    label="Today Sales"
    value="$245.00"
    trend="+8%"
    variant="success"
/>
```

---

## 20. Quick Action Card

Archivo sugerido:

```txt
resources/views/components/dashboard/quick-action-card.blade.php
```

### Uso

```blade
<x-dashboard.quick-action-card
    title="New Sale"
    description="Register a new customer sale."
    route="sales.create"
/>
```

---

# Componentes específicos del POS / inventario

## 21. Product Search Item

Archivo sugerido:

```txt
resources/views/components/sales/product-search-item.blade.php
```

### Uso

```blade
<x-sales.product-search-item
    name="Coca Cola 1L"
    sku="COCA-1L"
    price="$1.50"
    stock="25"
/>
```

---

## 22. Sale Item Row

Archivo sugerido:

```txt
resources/views/components/sales/sale-item-row.blade.php
```

### Contenido

```txt
Product name
Quantity input
Unit price
Subtotal
Remove button
```

---

## 23. Money Display

Archivo sugerido:

```txt
resources/views/components/ui/money.blade.php
```

### Uso

```blade
<x-ui.money :amount="$product->price" />
```

---

## 24. Status Dot

Archivo sugerido:

```txt
resources/views/components/ui/status-dot.blade.php
```

### Variants

```txt
success: emerald
warning: amber
danger: red
neutral: slate
```

### Uso

```blade
<x-ui.status-dot variant="success" label="Active" />
```

---

# Componentes mínimos recomendados para Sprint 01

Crear primero estos componentes:

```txt
x-ui.button
x-ui.input
x-ui.select
x-ui.card
x-ui.table
x-ui.badge
x-ui.alert
x-ui.modal
x-layout.app
x-layout.sidebar
x-layout.sidebar-link
x-layout.topbar
x-layout.page-header
```

Estos son suficientes para empezar Administración, Dashboard e Inventario sin repetir Tailwind.

---

# Reglas para implementar los Blade Components

1. Los componentes deben vivir en inglés.
2. Usar nombres consistentes.
3. No repetir clases Tailwind en cada vista.
4. Los formularios deben usar componentes.
5. Las tablas deben usar componentes.
6. Los botones de acciones deben usar variantes.
7. Livewire solo maneja estado y eventos.
8. La lógica de negocio sigue en Services o Actions.
9. No crear componentes demasiado complejos al inicio.
10. Mejor crear componentes simples, reutilizables y fáciles de mantener.

---

# Convención de nombres sugerida

```txt
resources/views/components/ui/button.blade.php
resources/views/components/ui/input.blade.php
resources/views/components/ui/select.blade.php
resources/views/components/ui/textarea.blade.php
resources/views/components/ui/card.blade.php
resources/views/components/ui/table.blade.php
resources/views/components/ui/badge.blade.php
resources/views/components/ui/alert.blade.php
resources/views/components/ui/modal.blade.php
resources/views/components/ui/dropdown.blade.php
resources/views/components/ui/error.blade.php
resources/views/components/ui/empty-state.blade.php
resources/views/components/layout/sidebar.blade.php
resources/views/components/layout/sidebar-link.blade.php
resources/views/components/layout/topbar.blade.php
resources/views/components/layout/page-header.blade.php
resources/views/components/dashboard/stat-card.blade.php
resources/views/components/dashboard/quick-action-card.blade.php
resources/views/components/sales/product-search-item.blade.php
resources/views/components/sales/sale-item-row.blade.php
```

---

# Recomendación final

Para Sprint 01, no intentes crear todos los componentes desde el primer día.

Primero crea los componentes base de UI y layout. Luego, cuando empieces Products, Customers y Sales, creas los componentes específicos según necesidad.

