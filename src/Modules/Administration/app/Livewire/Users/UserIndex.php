<?php

namespace Modules\Administration\Livewire\Users;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Administration\Models\User;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    // Form attributes
    public ?int $selectedUserId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public array $selectedRoles = [];

    protected function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $this->selectedUserId,
            'password'      => $this->selectedUserId ? 'nullable|min:8' : 'required|min:8',
            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'exists:roles,name',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name'          => __t('field_name', 'administration'),
            'email'         => __t('field_email', 'administration'),
            'password'      => __t('field_password', 'administration'),
            'selectedRoles' => __t('field_role', 'administration'),
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['selectedUserId', 'name', 'email', 'password', 'selectedRoles']);
        $this->selectedRoles = [];
        $this->resetValidation();
    }

    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->selectedUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->resetValidation();
    }

    public function openDeleteModal(int $userId): void
    {
        $this->selectedUserId = $userId;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->selectedUserId) {
            $user = User::findOrFail($this->selectedUserId);
            $data = [
                'name'  => $this->name,
                'email' => $this->email,
            ];
            if (!empty($this->password)) {
                $data['password'] = bcrypt($this->password);
            }
            $user->update($data);
            $user->syncRoles($this->selectedRoles);
        } else {
            $user = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => bcrypt($this->password),
            ]);
            $user->syncRoles($this->selectedRoles);
        }

        $this->reset(['selectedUserId', 'name', 'email', 'password', 'selectedRoles']);
        $this->selectedRoles = [];
        $this->dispatch('close-modal', 'user-form');
    }

    public function delete(): void
    {
        if ($this->selectedUserId) {
            User::destroy($this->selectedUserId);
        }
        $this->reset(['selectedUserId']);
        $this->dispatch('close-modal', 'delete-user');
    }

    public function render()
    {
        $users = User::query()
            ->with('roles')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        $roles = Role::all();

        return view('administration::livewire.users.user-index', [
            'users' => $users,
            'roles' => $roles,
            'totalUsers' => User::count(),
            'adminCount' => User::role('admin')->count(),
            'sellerCount' => User::role('seller')->count(),
        ]);
    }
}
