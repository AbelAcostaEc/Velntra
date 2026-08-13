<?php

namespace Modules\Administration\Livewire\Users;

// Framework & Livewire
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// Models
use Modules\Administration\Models\User;
use Spatie\Permission\Models\Role;

// Services
use Modules\Administration\Services\UserService;

#[Layout('layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    /**
     * Término de búsqueda para filtrar usuarios por nombre o email.
     */
    public string $search = '';

    /**
     * ID del usuario seleccionado para edición o eliminación (null en creación).
     */
    public ?int $selectedUserId = null;

    /**
     * Nombre del usuario en el formulario.
     */
    public string $name = '';

    /**
     * Correo electrónico del usuario en el formulario.
     */
    public string $email = '';

    /**
     * Contraseña del usuario (obligatoria en creación, opcional en edición).
     */
    public string $password = '';

    /**
     * Nombres de los roles asignados al usuario en el formulario.
     */
    public array $selectedRoles = [];

    /**
     * Reglas de validación dinámicas para la creación y edición de usuarios.
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users,email,' . $this->selectedUserId,
            'password'        => $this->selectedUserId ? 'nullable|min:8' : 'required|min:8',
            'selectedRoles'   => 'required|array|min:1',
            'selectedRoles.*' => 'exists:roles,name',
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
            'name'          => __t('field_name', 'administration'),
            'email'         => __t('field_email', 'administration'),
            'password'      => __t('field_password', 'administration'),
            'selectedRoles' => __t('field_role', 'administration'),
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
     * Preparar el formulario para crear un nuevo usuario y verificar permisos.
     */
    public function openCreateModal(): void
    {
        // Autorizar acción de creación mediante la Policy
        $this->authorize('create', User::class);

        // Limpiar campos del formulario y errores previos de validación
        $this->reset(['selectedUserId', 'name', 'email', 'password', 'selectedRoles']);
        $this->selectedRoles = [];
        $this->resetValidation();
    }

    /**
     * Cargar los datos de un usuario existente para edición y verificar permisos.
     */
    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);

        // Autorizar actualización sobre el modelo específico
        $this->authorize('update', $user);

        // Cargar datos en el formulario
        $this->selectedUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->resetValidation();
    }

    /**
     * Seleccionar el usuario que se desea eliminar y verificar permisos.
     */
    public function openDeleteModal(int $userId): void
    {
        $user = User::findOrFail($userId);

        // Autorizar eliminación sobre el modelo específico
        $this->authorize('delete', $user);

        $this->selectedUserId = $user->id;
    }

    /**
     * Guardar el usuario (crear nuevo o actualizar existente) mediante UserService.
     */
    public function save(UserService $userService): void
    {
        $this->validate();

        $userData = [
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
        ];

        if ($this->selectedUserId) {
            // Edición de usuario existente
            $user = User::findOrFail($this->selectedUserId);
            $this->authorize('update', $user);

            $userService->update($user, $userData, $this->selectedRoles);

            // Notificación toast de éxito al editar
            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('user_updated', 'administration'),
            ]);
        } else {
            // Creación de nuevo usuario
            $this->authorize('create', User::class);

            $userService->create($userData, $this->selectedRoles);

            // Notificación toast de éxito al crear
            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('user_created', 'administration'),
            ]);
        }

        // Limpiar estado y cerrar modal
        $this->reset(['selectedUserId', 'name', 'email', 'password', 'selectedRoles']);
        $this->selectedRoles = [];
        $this->dispatch('close-modal', 'user-form');
    }

    /**
     * Eliminar el usuario seleccionado mediante UserService.
     */
    public function delete(UserService $userService): void
    {
        if ($this->selectedUserId) {
            // Localizar y autorizar eliminación
            $user = User::findOrFail($this->selectedUserId);
            $this->authorize('delete', $user);

            $userService->delete($user);

            // Notificación toast de éxito al eliminar
            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('user_deleted', 'administration'),
            ]);
        }

        // Limpiar selección y cerrar modal de confirmación
        $this->reset(['selectedUserId']);
        $this->dispatch('close-modal', 'delete-user');
    }

    /**
     * Renderizar la vista del listado de usuarios y estadísticas con verificación de acceso.
     */
    public function render(): View
    {
        // Autorizar visualización general del listado
        $this->authorize('viewAny', User::class);

        // Consultar usuarios filtrados por búsqueda y paginados
        $users = User::query()
            ->with('roles')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        $roles = Role::all();

        return view('administration::livewire.users.user-index', [
            'users'       => $users,
            'roles'       => $roles,
            'totalUsers'  => User::count(),
            'adminCount'  => User::role('admin')->count(),
            'sellerCount' => User::role('seller')->count(),
        ]);
    }
}
