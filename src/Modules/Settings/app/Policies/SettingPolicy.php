<?php

namespace Modules\Settings\Policies;

// Models
use Modules\Administration\Models\User;
use Modules\Settings\Models\Setting;

class SettingPolicy
{
    /**
     * Determinar si el usuario autenticado puede ver la configuración general.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('settings.view');
    }

    /**
     * Determinar si el usuario autenticado puede ver el detalle de configuración.
     *
     * @param User $user
     * @param Setting|null $setting
     * @return bool
     */
    public function view(User $user, ?Setting $setting = null): bool
    {
        return $user->can('settings.view');
    }

    /**
     * Determinar si el usuario autenticado puede actualizar la configuración general.
     *
     * @param User $user
     * @param Setting|null $setting
     * @return bool
     */
    public function update(User $user, ?Setting $setting = null): bool
    {
        return $user->can('settings.update');
    }
}
