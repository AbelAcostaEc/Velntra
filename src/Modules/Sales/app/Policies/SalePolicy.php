<?php

namespace Modules\Sales\Policies;

// Models
use Modules\Administration\Models\User;
use Modules\Sales\Models\Sale;

class SalePolicy
{
    /**
     * Determinar si el usuario puede ver el listado o pantalla de ventas.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('sales.view');
    }

    /**
     * Determinar si el usuario puede ver el detalle de una venta.
     *
     * @param User $user
     * @param Sale $sale
     * @return bool
     */
    public function view(User $user, Sale $sale): bool
    {
        return $user->can('sales.view');
    }

    /**
     * Determinar si el usuario puede crear ventas en el POS.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('sales.create');
    }

    /**
     * Determinar si el usuario puede actualizar una venta pendiente.
     *
     * @param User $user
     * @param Sale $sale
     * @return bool
     */
    public function update(User $user, Sale $sale): bool
    {
        return $user->can('sales.update');
    }

    /**
     * Determinar si el usuario puede anular una venta completada.
     *
     * @param User $user
     * @param Sale $sale
     * @return bool
     */
    public function cancel(User $user, Sale $sale): bool
    {
        return $user->can('sales.cancel') && $sale->isCompleted();
    }

    /**
     * Determinar si el usuario puede eliminar una venta en espera.
     *
     * @param User $user
     * @param Sale $sale
     * @return bool
     */
    public function delete(User $user, Sale $sale): bool
    {
        return $user->can('sales.delete') || ($user->can('sales.create') && $sale->isPending());
    }
}
