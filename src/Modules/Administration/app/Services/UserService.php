<?php

namespace Modules\Administration\Services;

// Framework & Database
use Illuminate\Support\Facades\DB;

// Models
use Modules\Administration\Models\User;

class UserService
{
    /**
     * Buscar un usuario por su ID o lanzar una excepción ModelNotFoundException.
     *
     * @param int $id
     * @return User
     */
    public function find(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Crear un nuevo usuario y sincronizar sus roles en una transacción de BD.
     *
     * @param array{name: string, email: string, password: string} $data
     * @param array<string> $roles
     * @return User
     */
    public function create(array $data, array $roles = []): User
    {
        return DB::transaction(function () use ($data, $roles) {
            // Crear el registro de usuario (el cast 'hashed' del modelo maneja la contraseña)
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);

            // Asignar roles si se especificaron
            if (!empty($roles)) {
                $user->syncRoles($roles);
            }

            return $user;
        });
    }

    /**
     * Actualizar datos de un usuario existente y sincronizar sus roles.
     *
     * @param User $user
     * @param array{name: string, email: string, password?: string|null} $data
     * @param array<string> $roles
     * @return User
     */
    public function update(User $user, array $data, array $roles = []): User
    {
        return DB::transaction(function () use ($user, $data, $roles) {
            $updateData = [
                'name'  => $data['name'],
                'email' => $data['email'],
            ];

            // Solo actualizar la contraseña si se proporciona un nuevo valor
            if (!empty($data['password'])) {
                $updateData['password'] = $data['password'];
            }

            $user->update($updateData);

            // Sincronizar la lista actualizada de roles asignados
            $user->syncRoles($roles);

            return $user;
        });
    }

    /**
     * Eliminar un usuario de forma segura dentro de una transacción de BD.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            return (bool) $user->delete();
        });
    }
}
