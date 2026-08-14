<?php

namespace Modules\Administration\Livewire\Roles;

// Framework & Livewire
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// Models
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Services
use Modules\Administration\Services\RoleService;

#[Layout('layouts.app')]
class RoleIndex extends Component
{
    use WithPagination;

    /**
     * Término de búsqueda para filtrar roles por nombre.
     */
    public string $search = '';

    /**
     * Cantidad de registros por página en la tabla.
     */
    public int $perPage = 10;

    /**
     * Reiniciar la paginación al cambiar la cantidad de items por página.
     */
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * ID del rol seleccionado para edición o eliminación (null en creación).
     */
    public ?int $selectedRoleId = null;

    /**
     * Nombre del rol en el formulario.
     */
    public string $name = '';

    /**
     * Nombres de los permisos asignados al rol en el formulario.
     *
     * @var array<string>
     */
    public array $selectedPermissions = [];

    /**
     * Reglas de validación dinámicas para la creación y edición de roles.
     *
     * @return array<string, array<int, mixed>|string>
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($this->selectedRoleId),
            ],
            'selectedPermissions'   => 'nullable|array',
            'selectedPermissions.*' => 'exists:permissions,name',
        ];
    }

    /**
     * Nombres de atributos personalizados para los mensajes de validación.
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name'                => __t('field_role_name', 'administration'),
            'selectedPermissions' => __t('field_permissions', 'administration'),
        ];
    }

    /**
     * Reiniciar la paginación al actualizar el campo de búsqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Preparar el formulario para crear un nuevo rol y verificar permisos.
     */
    public function openCreateModal(): void
    {
        // Autorizar acción de creación mediante la Policy
        $this->authorize('create', Role::class);

        // Limpiar campos del formulario y errores previos de validación
        $this->reset(['selectedRoleId', 'name', 'selectedPermissions']);
        $this->selectedPermissions = [];
        $this->resetValidation();
    }

    /**
     * Cargar los datos de un rol existente para edición y verificar permisos.
     */
    public function openEditModal(int $roleId): void
    {
        $role = Role::with('permissions')->findOrFail($roleId);

        // Autorizar actualización sobre el modelo específico
        $this->authorize('update', $role);

        // Cargar datos en el formulario
        $this->selectedRoleId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->resetValidation();
    }

    /**
     * Seleccionar el rol que se desea eliminar y verificar permisos.
     */
    public function openDeleteModal(int $roleId): void
    {
        $role = Role::findOrFail($roleId);

        // Autorizar eliminación sobre el modelo específico
        $this->authorize('delete', $role);

        $this->selectedRoleId = $role->id;
    }

    /**
     * Guardar el rol (crear nuevo o actualizar existente) mediante RoleService.
     */
    public function save(RoleService $roleService): void
    {
        $this->validate();

        $roleData = [
            'name' => strtolower(trim($this->name)),
        ];

        if ($this->selectedRoleId) {
            // Edición de rol existente
            $role = Role::findOrFail($this->selectedRoleId);
            $this->authorize('update', $role);

            $roleService->update($role, $roleData, $this->selectedPermissions);

            // Notificación toast de éxito al editar
            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('role_updated', 'administration'),
            ]);
        } else {
            // Creación de nuevo rol
            $this->authorize('create', Role::class);

            $roleService->create($roleData, $this->selectedPermissions);

            // Notificación toast de éxito al crear
            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('role_created', 'administration'),
            ]);
        }

        // Limpiar estado y cerrar modal
        $this->reset(['selectedRoleId', 'name', 'selectedPermissions']);
        $this->selectedPermissions = [];
        $this->dispatch('close-modal', 'role-form');
    }

    /**
     * Eliminar el rol seleccionado mediante RoleService.
     */
    public function delete(RoleService $roleService): void
    {
        if ($this->selectedRoleId) {
            // Localizar y autorizar eliminación
            $role = Role::findOrFail($this->selectedRoleId);
            $this->authorize('delete', $role);

            $roleService->delete($role);

            // Notificación toast de éxito al eliminar
            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('role_deleted', 'administration'),
            ]);
        }

        // Limpiar selección y cerrar modal de confirmación
        $this->reset(['selectedRoleId']);
        $this->dispatch('close-modal', 'delete-role');
    }

    /**
     * Seleccionar todos los permisos disponibles del sistema.
     */
    public function selectAllPermissions(): void
    {
        $this->selectedPermissions = Permission::pluck('name')->toArray();
    }

    /**
     * Deseleccionar todos los permisos.
     */
    public function deselectAllPermissions(): void
    {
        $this->selectedPermissions = [];
    }

    /**
     * Alternar todos los permisos pertenecientes a un módulo específico.
     *
     * @param array<string> $permissionNames
     */
    public function toggleModulePermissions(array $permissionNames): void
    {
        $hasAll = count(array_intersect($permissionNames, $this->selectedPermissions)) === count($permissionNames);

        if ($hasAll) {
            // Remover los permisos de este módulo
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $permissionNames));
        } else {
            // Agregar los permisos que faltan
            $this->selectedPermissions = array_values(array_unique(array_merge($this->selectedPermissions, $permissionNames)));
        }
    }

    /**
     * Renderizar la vista del listado de roles y estadísticas con verificación de acceso.
     */
    public function render(): View
    {
        // Autorizar visualización general del listado
        $this->authorize('viewAny', Role::class);

        // Consultar roles filtrados por búsqueda y con conteos de relaciones
        $roles = Role::query()
            ->with(['permissions', 'users'])
            ->withCount(['permissions', 'users'])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate($this->perPage);

        // Agrupar permisos por prefijo de módulo (ej. users, roles, categories)
        $allPermissions = Permission::orderBy('name')->get();
        $permissionsByModule = $allPermissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'general';
        });

        // Contar usuarios que tienen al menos un rol asignado
        $assignedUsersCount = \Modules\Administration\Models\User::has('roles')->count();

        return view('administration::livewire.roles.role-index', [
            'roles'                => $roles,
            'permissionsByModule'  => $permissionsByModule,
            'totalRoles'           => Role::count(),
            'totalPermissions'     => Permission::count(),
            'assignedUsersCount'   => $assignedUsersCount,
        ]);
    }
}
