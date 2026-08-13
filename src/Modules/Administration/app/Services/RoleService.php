<?php

namespace Modules\Administration\Services;

// Framework & Database
use Illuminate\Support\Facades\DB;

// Models
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * Buscar un rol por su ID o lanzar una excepción ModelNotFoundException.
     *
     * @param int $id
     * @return Role
     */
    public function find(int $id): Role
    {
        return Role::findOrFail($id);
    }

    /**
     * Crear un nuevo rol y sincronizar sus permisos dentro de una transacción de BD.
     *
     * @param array{name: string, guard_name?: string} $data
     * @param array<string> $permissions
     * @return Role
     */
    public function create(array $data, array $permissions = []): Role
    {
        return DB::transaction(function () use ($data, $permissions) {
            $role = Role::create([
                'name'       => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'web',
            ]);

            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
            }

            return $role;
        });
    }

    /**
     * Actualizar datos de un rol existente y sincronizar sus permisos dentro de una transacción de BD.
     *
     * @param Role $role
     * @param array{name: string} $data
     * @param array<string> $permissions
     * @return Role
     */
    public function update(Role $role, array $data, array $permissions = []): Role
    {
        return DB::transaction(function () use ($role, $data, $permissions) {
            $role->update([
                'name' => $data['name'],
            ]);

            $role->syncPermissions($permissions);

            return $role;
        });
    }

    /**
     * Eliminar un rol de forma segura dentro de una transacción de BD.
     *
     * @param Role $role
     * @return bool
     */
    public function delete(Role $role): bool
    {
        return DB::transaction(function () use ($role) {
            return (bool) $role->delete();
        });
    }
}
