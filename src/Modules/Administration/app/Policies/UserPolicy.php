<?php

namespace Modules\Administration\Policies;

// Models
use Modules\Administration\Models\User;

class UserPolicy
{
    /**
     * Determinar si el usuario autenticado puede ver el listado de usuarios.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    /**
     * Determinar si el usuario autenticado puede ver el detalle de un usuario.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        return $user->can('users.view');
    }

    /**
     * Determinar si el usuario autenticado puede crear nuevos usuarios.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    /**
     * Determinar si el usuario autenticado puede actualizar el usuario especificado.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $user->can('users.update');
    }

    /**
     * Determinar si el usuario autenticado puede eliminar el usuario especificado.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can('users.delete');
    }
}
