# 📋 Plantillas Base Reutilizables para CRUDs - Velntra v1.0

Usa estas plantillas como punto de partida exacto al crear nuevos CRUDs o refactorizar los existentes en cualquier módulo (`Inventory`, `Customers`, `Sales`, `Settings`, etc.).

Reemplaza los marcadores:
- `{Module}` -> Nombre del módulo en PascalCase (ej. `Inventory`)
- `{module}` -> Nombre del módulo en minúsculas (ej. `inventory`)
- `{Model}`  -> Nombre del modelo en Singular PascalCase (ej. `Product`)
- `{model}`  -> Nombre del modelo en snake_case/minúsculas (ej. `product`)
- `{feature}`-> Nombre de la entidad/carpeta (ej. `products`)

---

## 1. Plantilla Componente Livewire (`app/Livewire/{Feature}/{Model}Index.php`)

```php
<?php

namespace Modules\{Module}\Livewire\{Feature};

// Framework & Livewire
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// Models
use Modules\{Module}\Models\{Model};

// Services
use Modules\{Module}\Services\{Model}Service;

#[Layout('layouts.app')]
class {Model}Index extends Component
{
    use WithPagination;

    /**
     * Término de búsqueda para filtrar registros.
     */
    public string $search = '';

    /**
     * ID del registro seleccionado para edición o eliminación (null en creación).
     */
    public ?int $selected{Model}Id = null;

    /**
     * Propiedades del formulario.
     */
    public string $name = '';
    // ... agregar propiedades adicionales

    /**
     * Reglas de validación dinámicas.
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            // ... reglas adicionales
        ];
    }

    /**
     * Nombres de atributos personalizados para validación.
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => __t('field_name', '{module}'),
        ];
    }

    /**
     * Reiniciar la paginación al actualizar la búsqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Preparar el formulario para crear un nuevo registro y verificar permisos.
     */
    public function openCreateModal(): void
    {
        $this->authorize('create', {Model}::class);

        $this->reset(['selected{Model}Id', 'name']);
        $this->resetValidation();
    }

    /**
     * Cargar los datos de un registro para edición y verificar permisos.
     */
    public function openEditModal(int $id): void
    {
        $item = {Model}::findOrFail($id);
        $this->authorize('update', $item);

        $this->selected{Model}Id = $item->id;
        $this->name = $item->name;
        $this->resetValidation();
    }

    /**
     * Seleccionar el registro que se desea eliminar y verificar permisos.
     */
    public function openDeleteModal(int $id): void
    {
        $item = {Model}::findOrFail($id);
        $this->authorize('delete', $item);

        $this->selected{Model}Id = $item->id;
    }

    /**
     * Guardar el registro (creación o actualización) mediante {Model}Service.
     */
    public function save({Model}Service $service): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
        ];

        if ($this->selected{Model}Id) {
            $item = {Model}::findOrFail($this->selected{Model}Id);
            $this->authorize('update', $item);

            $service->update($item, $data);

            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('{model}_updated', '{module}'),
            ]);
        } else {
            $this->authorize('create', {Model}::class);

            $service->create($data);

            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('{model}_created', '{module}'),
            ]);
        }

        $this->reset(['selected{Model}Id', 'name']);
        $this->dispatch('close-modal', '{model}-form');
    }

    /**
     * Eliminar el registro seleccionado mediante {Model}Service.
     */
    public function delete({Model}Service $service): void
    {
        if ($this->selected{Model}Id) {
            $item = {Model}::findOrFail($this->selected{Model}Id);
            $this->authorize('delete', $item);

            $service->delete($item);

            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('{model}_deleted', '{module}'),
            ]);
        }

        $this->reset(['selected{Model}Id']);
        $this->dispatch('close-modal', 'delete-{model}');
    }

    /**
     * Renderizar la vista con autorización y datos paginados.
     */
    public function render(): View
    {
        $this->authorize('viewAny', {Model}::class);

        $items = {Model}::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return view('{module}::livewire.{feature}.{model}-index', [
            'items'      => $items,
            'totalItems' => {Model}::count(),
        ]);
    }
}
```

---

## 2. Plantilla Servicio (`app/Services/{Model}Service.php`)

```php
<?php

namespace Modules\{Module}\Services;

// Framework & Database
use Illuminate\Support\Facades\DB;

// Models
use Modules\{Module}\Models\{Model};

class {Model}Service
{
    /**
     * Buscar un registro por su ID o fallar.
     */
    public function find(int $id): {Model}
    {
        return {Model}::findOrFail($id);
    }

    /**
     * Crear un nuevo registro dentro de una transacción.
     */
    public function create(array $data): {Model}
    {
        return DB::transaction(function () use ($data) {
            return {Model}::create($data);
        });
    }

    /**
     * Actualizar un registro existente dentro de una transacción.
     */
    public function update({Model} $item, array $data): {Model}
    {
        return DB::transaction(function () use ($item, $data) {
            $item->update($data);
            return $item;
        });
    }

    /**
     * Eliminar un registro dentro de una transacción.
     */
    public function delete({Model} $item): bool
    {
        return DB::transaction(function () use ($item) {
            return (bool) $item->delete();
        });
    }
}
```

---

## 3. Plantilla Política (`app/Policies/{Model}Policy.php`)

```php
<?php

namespace Modules\{Module}\Policies;

// Models
use Modules\Administration\Models\User;
use Modules\{Module}\Models\{Model};

class {Model}Policy
{
    /**
     * Determinar si el usuario puede listar los registros.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('{module}.view');
    }

    /**
     * Determinar si el usuario puede ver el detalle del registro.
     */
    public function view(User $user, {Model} $item): bool
    {
        return $user->can('{module}.view');
    }

    /**
     * Determinar si el usuario puede crear registros.
     */
    public function create(User $user): bool
    {
        return $user->can('{module}.create');
    }

    /**
     * Determinar si el usuario puede actualizar el registro.
     */
    public function update(User $user, {Model} $item): bool
    {
        return $user->can('{module}.update');
    }

    /**
     * Determinar si el usuario puede eliminar el registro.
     */
    public function delete(User $user, {Model} $item): bool
    {
        return $user->can('{module}.delete');
    }
}
```

---

## 4. Plantilla Registro de Política (`app/Providers/{Module}ServiceProvider.php`)

```php
use Illuminate\Support\Facades\Gate;
use Modules\{Module}\Models\{Model};
use Modules\{Module}\Policies\{Model}Policy;

class {Module}ServiceProvider extends ModuleServiceProvider
{
    /**
     * Políticas registradas para el módulo.
     *
     * @var array<class-string, class-string>
     */
    protected array $policies = [
        {Model}::class => {Model}Policy::class,
    ];

    public function boot(): void
    {
        parent::boot();
        $this->registerPolicies();
    }

    protected function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
```

---

## 5. Plantilla Vista Blade (`resources/views/livewire/{feature}/{model}-index.blade.php`)

```blade
<div>
    <x-crud-page :title="__t('{feature}_management', '{module}')" :description="__t('{feature}_description', '{module}')">
        <x-slot:actions>
            @can('create', \Modules\{Module}\Models\{Model}::class)
                <x-button
                    wire:click="openCreateModal"
                    x-on:click="$dispatch('open-modal', '{model}-form')"
                    wire:loading.attr="disabled">
                    <svg class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __t('create_{model}', '{module}') }}
                </x-button>
            @endcan
        </x-slot:actions>

        <x-slot:summary>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <x-stat-card :label="__t('total_{feature}', '{module}')" value="{{ $totalItems }}" :trend="__t('registered', '{module}')" variant="info" />
            </div>
        </x-slot:summary>

        <x-slot:content>
            <x-table loading-target="search">
                <x-slot:toolbar>
                    <x-table-search :placeholder="__t('search_placeholder', '{module}')" wire:model.live.debounce.300ms="search" />
                </x-slot:toolbar>

                <x-slot:head>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_name', '{module}') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_actions', '{module}') }}</th>
                    </tr>
                </x-slot:head>

                @forelse($items as $item)
                    <tr class="hover:bg-primary-50/50 transition-colors">
                        <td class="px-4 py-4 text-sm font-medium text-primary-950">{{ $item->name }}</td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('update', $item)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $item->id }})"
                                        x-on:click="$dispatch('open-modal', '{model}-form')">
                                        {{ __t('edit', '{module}') }}
                                    </x-button>
                                @endcan
                                @can('delete', $item)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $item->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-{model}')">
                                        {{ __t('delete', '{module}') }}
                                    </x-button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">
                            <x-empty-state
                                :title="__t('no_{feature}_title', '{module}')"
                                :description="__t('no_{feature}_description', '{module}')" />
                        </td>
                    </tr>
                @endforelse

                <x-slot:mobile>
                    @foreach ($items as $item)
                        <x-mobile-record-card
                            :title="$item->name">
                            <x-slot:actions>
                                @can('update', $item)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $item->id }})"
                                        x-on:click="$dispatch('open-modal', '{model}-form')">
                                        {{ __t('edit', '{module}') }}
                                    </x-button>
                                @endcan
                                @can('delete', $item)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $item->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-{model}')">
                                        {{ __t('delete', '{module}') }}
                                    </x-button>
                                @endcan
                            </x-slot:actions>
                        </x-mobile-record-card>
                    @endforeach
                </x-slot:mobile>

                <x-slot:pagination>
                    <div class="px-4 py-3">
                        {{ $items->links() }}
                    </div>
                </x-slot:pagination>
            </x-table>
        </x-slot:content>
    </x-crud-page>

    {{-- Modal Formulario Crear / Editar --}}
    <x-modal
        name="{model}-form"
        :title="$selected{Model}Id ? __t('edit_{model}', '{module}') : __t('create_{model}', '{module}')"
        :description="__t('form_description', '{module}')"
        loading-target="openCreateModal, openEditModal">
        <form wire:submit.prevent="save" class="space-y-5 p-6">
            <x-input
                :label="__t('field_name', '{module}')"
                name="name"
                wire:model="name"
                required />

            <div class="flex justify-end gap-2 border-t border-primary-200 pt-5">
                <x-button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close-modal', '{model}-form')">
                    {{ __t('cancel', '{module}') }}
                </x-button>

                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __t('save', '{module}') }}</span>
                    <span wire:loading>{{ __t('saving', '{module}') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Confirmación de Eliminación --}}
    <x-confirm-dialog
        name="delete-{model}"
        :title="__t('delete_{model}_title', '{module}')"
        :description="__t('delete_{model}_description', '{module}')"
        :confirm-label="__t('delete', '{module}')"
        :cancel-label="__t('cancel', '{module}')"
        action="delete"
        loading-target="openDeleteModal, delete" />
</div>
```

---

## 6. Plantilla de Archivo de Idioma (`lang/{es,en}/layout.php`)

```php
<?php

return [
    // Page
    '{feature}_management'             => 'Gestión de {Model}',
    '{feature}_description'            => 'Administra los registros del sistema.',

    // Stats
    'total_{feature}'                  => 'Total {Model}',
    'registered'                       => 'Registrados',

    // Table
    'search_placeholder'               => 'Buscar...',
    'col_name'                         => 'Nombre',
    'col_actions'                      => 'Acciones',

    // Empty state
    'no_{feature}_title'               => 'No hay registros',
    'no_{feature}_description'         => 'No se encontraron registros que coincidan.',

    // Actions
    'edit'                             => 'Editar',
    'delete'                           => 'Eliminar',
    'create_{model}'                   => 'Crear {Model}',
    'edit_{model}'                     => 'Editar {Model}',
    'form_description'                 => 'Completa los campos a continuación.',
    'cancel'                           => 'Cancelar',
    'save'                             => 'Guardar',
    'saving'                           => 'Guardando...',

    // Fields
    'field_name'                       => 'Nombre',

    // Delete
    'delete_{model}_title'             => 'Eliminar {Model}',
    'delete_{model}_description'       => '¿Estás seguro de que deseas eliminar este registro?',

    // Toast
    '{model}_created'                  => '{Model} creado exitosamente.',
    '{model}_updated'                  => '{Model} actualizado exitosamente.',
    '{model}_deleted'                  => '{Model} eliminado exitosamente.',
];
```

---

## 7. Plantilla de Pruebas Feature (`tests/Feature/{Model}ManagementTest.php`)

```php
<?php

namespace Modules\{Module}\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Administration\Models\User;
use Modules\{Module}\Livewire\{Feature}\{Model}Index;
use Modules\{Module}\Models\{Model};
use Modules\{Module}\Services\{Model}Service;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class {Model}ManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $viewerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            '{module}.view',
            '{module}.create',
            '{module}.update',
            '{module}.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@velntra.test',
            'password' => bcrypt('password'),
        ]);
        $this->adminUser->assignRole('admin');

        $this->viewerUser = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@velntra.test',
            'password' => bcrypt('password'),
        ]);
        $this->viewerUser->givePermissionTo('{module}.view');
    }

    public function test_service_creates_and_updates_record(): void
    {
        $service = app({Model}Service::class);

        $item = $service->create(['name' => 'Test Item']);
        $this->assertDatabaseHas('{table}', ['name' => 'Test Item']);

        $updated = $service->update($item, ['name' => 'Updated Item']);
        $this->assertEquals('Updated Item', $updated->name);

        $service->delete($updated);
        $this->assertDatabaseMissing('{table}', ['id' => $item->id]);
    }

    public function test_index_component_authorizes_and_renders(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test({Model}Index::class)
            ->assertStatus(200);
    }

    public function test_index_component_creates_record_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test({Model}Index::class)
            ->set('name', 'Livewire Item')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal', '{model}-form')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('{table}', ['name' => 'Livewire Item']);
    }

    public function test_index_hides_mutation_buttons_for_view_only_user(): void
    {
        $this->actingAs($this->viewerUser);

        Livewire::test({Model}Index::class)
            ->assertStatus(200)
            ->assertDontSeeHtml('wire:click="openCreateModal"')
            ->assertDontSeeHtml('wire:click="openEditModal')
            ->assertDontSeeHtml('wire:click="openDeleteModal');
    }
}
```
