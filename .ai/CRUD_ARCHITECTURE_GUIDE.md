# 📖 Guía de Arquitectura para CRUDs - Velntra v1.0

Esta guía describe el estándar técnico y arquitectónico oficial para crear o refactorizar cualquier CRUD dentro de la arquitectura **Modular Monolith** de Velntra.

---

## 1. Separación de Responsabilidades

Cada CRUD debe descomponerse en capas con responsabilidades únicas y estrictas:

```text
┌─────────────────────────────────────────────────────────────┐
│ 1. Blade View                                               │
│    - Renderizado visual (x-crud-page, x-table, x-modal)     │
│    - Directivas @can para ocultar/mostrar botones           │
│    - Soporte responsivo (Tabla escritorio + Tarjetas móvil) │
│    - Estados de carga (loading-target)                      │
│    - Traducciones con __t('key', 'module')                  │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│ 2. Livewire Component                                       │
│    - Manejo del estado temporal de UI                       │
│    - Paginación y filtros de búsqueda (WithPagination)       │
│    - Validación de formulario ($this->validate / rules())   │
│    - Autorización explícita ($this->authorize)              │
│    - Emisión de eventos (close-modal, toast)                │
│    - Inyección de dependencias del Service                  │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│ 3. Policy Layer                                             │
│    - Autorización exclusiva mediante permisos de Spatie     │
│    - Convención module.action (ej. users.create)            │
│    - Métodos: viewAny, view, create, update, delete         │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│ 4. Service Layer                                            │
│    - Toda la lógica de negocio y persistencia               │
│    - Transacciones atómicas (DB::transaction)               │
│    - Mutaciones, hashing de contraseñas, sync de relaciones │
│    - Reutilizable desde Livewire, API, Commands o Jobs      │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│ 5. Eloquent Model & Database                                │
│    - Definición de casts, fillables, relaciones y scopes    │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. Flujo de Ejecución Canónico

### A. Listado y Carga Inicial (`render`)
1. Se verifica la autorización general: `$this->authorize('viewAny', Model::class);`.
2. Se consultan los registros aplicando filtros (`when($this->search, ...)`), relaciones `with(...)` y paginación `paginate(10)`.
3. Se cargan estadísticas para `x-stat-card`.
4. La vista Blade valida con `@can` antes de renderizar botones de mutación.

### B. Apertura de Modales (`openCreateModal`, `openEditModal`, `openDeleteModal`)
1. Se autoriza la acción:
   - Crear: `$this->authorize('create', Model::class);`
   - Editar: `$this->authorize('update', $model);`
   - Eliminar: `$this->authorize('delete', $model);`
2. Se inicializa el estado del componente (limpieza de errores previos con `$this->resetValidation()`).
3. En la vista, el modal muestra su overlay animado de carga (`loading-target="openCreateModal, openEditModal"`) mientras Livewire completa la llamada.

### C. Guardado (`save(ModelService $service)`)
1. Se ejecuta la validación: `$this->validate();`.
2. Si existe `$selectedId`:
   - Se localiza el modelo: `$model = Model::findOrFail($this->selectedId);`.
   - Se autoriza: `$this->authorize('update', $model);`.
   - Se delega al servicio: `$service->update($model, $data, $relations);`.
   - Se despacha notificación Toast de actualización.
3. Si no existe `$selectedId`:
   - Se autoriza: `$this->authorize('create', Model::class);`.
   - Se delega al servicio: `$service->create($data, $relations);`.
   - Se despacha notificación Toast de creación.
4. Se resetea el estado del formulario y se cierra el modal con `$this->dispatch('close-modal', 'modal-name')`.

### D. Eliminación (`delete(ModelService $service)`)
1. Se localiza el modelo y se autoriza: `$this->authorize('delete', $model);`.
2. Se delega al servicio: `$service->delete($model);`.
3. Se despacha notificación Toast de eliminación y se cierra el modal de confirmación.

---

## 3. Estándares por Capa de Código

### A. Componente Livewire (`app/Livewire/{Feature}/{Model}Index.php`)
- **Organización de Importaciones**:
  ```php
  // Framework & Livewire
  use Illuminate\Contracts\View\View;
  use Livewire\Attributes\Layout;
  use Livewire\Component;
  use Livewire\WithPagination;

  // Models
  use Modules\{Module}\Models\{Model};

  // Services
  use Modules\{Module}\Services\{Model}Service;
  ```
- **Documentación**:
  - Comentario Docblock en la clase y en cada propiedad pública.
  - Docblock conciso en cada método (`updatingSearch`, `openCreateModal`, `openEditModal`, `openDeleteModal`, `save`, `delete`, `render`).
  - Comentarios internos en bloques lógicos.
- **Inyección del Servicio**: Inyectar `ModelService` directamente en los métodos de acción `save(ModelService $service)` y `delete(ModelService $service)`.
- **Reglas Dinámicas**: Usar el método `rules()` para adaptar validaciones condicionales (ej. contraseñas obligatorias en creación pero `nullable` en edición, y exclusión de ID en reglas `unique`).

### B. Capa de Servicio (`app/Services/{Model}Service.php`)
- **Organización de Importaciones**:
  ```php
  // Framework & Database
  use Illuminate\Support\Facades\DB;

  // Models
  use Modules\{Module}\Models\{Model};
  ```
- **Transacciones**: Todo método mutador (`create`, `update`, `delete`) debe envolverse en `DB::transaction(function () { ... })`.
- **Métodos requeridos**:
  - `find(int $id): Model`
  - `create(array $data, ...): Model`
  - `update(Model $model, array $data, ...): Model`
  - `delete(Model $model): bool`

### C. Capa de Política (`app/Policies/{Model}Policy.php`)
- **Organización de Importaciones**:
  ```php
  // Models
  use Modules\Administration\Models\User;
  use Modules\{Module}\Models\{Model};
  ```
- **Permisos de Spatie**: Cada método debe consultar el permiso correspondiente con `$user->can('{module}.{action}')`:
  - `viewAny(User $user): bool` -> `$user->can('{module}.view')`
  - `view(User $user, Model $model): bool` -> `$user->can('{module}.view')`
  - `create(User $user): bool` -> `$user->can('{module}.create')`
  - `update(User $user, Model $model): bool` -> `$user->can('{module}.update')`
  - `delete(User $user, Model $model): bool` -> `$user->can('{module}.delete')`

### D. Registro de la Política en el Proveedor (`app/Providers/{Module}ServiceProvider.php`)
- Registrar explícitamente en el método `boot()`:
  ```php
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
  ```

### E. Vista Blade (`resources/views/livewire/{feature}/{model}-index.blade.php`)
- **Estructura base**:
  - `<x-crud-page :title="..." :description="...">`
  - `<x-slot:actions>` con `@can('create', Model::class)`
  - `<x-slot:summary>` con `<x-stat-card>`
  - `<x-slot:content>` con `<x-table loading-target="search">`
  - Tabla de escritorio con columnas y botones `@can('update', $item)` y `@can('delete', $item)`.
  - `<x-slot:mobile>` con `<x-mobile-record-card>` iterando registros y exponiendo `<x-slot:status>` y `<x-slot:actions>`.
  - `<x-modal name="{model}-form" loading-target="openCreateModal, openEditModal">`
  - `<x-confirm-dialog name="delete-{model}" action="delete" loading-target="openDeleteModal, delete" />`
- **Textos Traducibles**: Utilizar siempre el helper `__t('key', '{module}')`.

---

## 4. Notificaciones Toast

Al finalizar operaciones de mutación, el componente Livewire debe emitir el evento `toast`:

```php
$this->dispatch('toast', [
    'type'    => 'success', // 'success' | 'warning' | 'danger' | 'info'
    'message' => __t('user_created', 'administration'),
]);
```

### Claves de traducción requeridas en `lang/{es,en}/layout.php`:
- `{model}_created`
- `{model}_updated`
- `{model}_deleted`

---

## 5. Pruebas Automatizadas (Feature Testing)

Todo CRUD debe contar con su correspondiente archivo de pruebas en `tests/Feature/{Model}ManagementTest.php` cubriendo:

1. **Service Tests**:
   - Creación correcta con relaciones/roles.
   - Actualización correcta (incluyendo campos opcionales/condicionales).
   - Eliminación correcta.
2. **Policy Tests**:
   - Acceso autorizado para usuarios con rol/permisos (Status 200).
   - Acceso denegado (403 Forbidden) para usuarios sin permisos.
3. **Livewire Component Tests**:
   - Creación vía Livewire con emisión de evento `toast` y `close-modal`.
   - Edición vía Livewire con carga previa de datos y guardado.
   - Eliminación vía Livewire con confirmación y evento `toast`.
4. **UI Visibility Tests**:
   - Los botones de mutación se muestran cuando el usuario tiene permisos.
   - Los botones de mutación se ocultan para usuarios de solo lectura (`assertDontSeeHtml('wire:click="openCreateModal"')`).
