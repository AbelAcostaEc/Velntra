<?php

namespace Modules\Administration\Policies;

// Models
use Modules\Administration\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Determinar si el usuario autenticado puede ver el listado de roles.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('roles.view');
    }

    /**
     * Determinar si el usuario autenticado puede ver el detalle de un rol.
     *
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.view');
    }

    /**
     * Determinar si el usuario autenticado puede crear nuevos roles.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    /**
     * Determinar si el usuario autenticado puede actualizar el rol especificado.
     *
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can('roles.update');
    }

    /**
     * Determinar si el usuario autenticado puede eliminar el rol especificado.
     *
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->can('roles.delete');
    }
}
