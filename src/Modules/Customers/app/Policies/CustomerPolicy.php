<?php

namespace Modules\Customers\Policies;

// Models
use Modules\Administration\Models\User;
use Modules\Customers\Models\Customer;

class CustomerPolicy
{
    /**
     * Determinar si el usuario autenticado puede ver el listado de clientes.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customers.view');
    }

    /**
     * Determinar si el usuario autenticado puede ver el detalle de un cliente.
     *
     * @param User $user
     * @param Customer $customer
     * @return bool
     */
    public function view(User $user, Customer $customer): bool
    {
        return $user->can('customers.view');
    }

    /**
     * Determinar si el usuario autenticado puede crear nuevos clientes.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('customers.create');
    }

    /**
     * Determinar si el usuario autenticado puede actualizar el cliente especificado.
     *
     * @param User $user
     * @param Customer $customer
     * @return bool
     */
    public function update(User $user, Customer $customer): bool
    {
        return $user->can('customers.update');
    }

    /**
     * Determinar si el usuario autenticado puede eliminar el cliente especificado.
     * No se permite eliminar al cliente Consumidor Final (Regla BR-018).
     *
     * @param User $user
     * @param Customer $customer
     * @return bool
     */
    public function delete(User $user, Customer $customer): bool
    {
        if ($customer->isConsumidorFinal()) {
            return false;
        }

        return $user->can('customers.delete');
    }
}
