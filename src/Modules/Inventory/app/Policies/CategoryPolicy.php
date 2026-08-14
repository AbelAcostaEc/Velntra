<?php

namespace Modules\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Administration\Models\User;
use Modules\Inventory\Models\Category;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any categories.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('categories.view');
    }

    /**
     * Determine whether the user can view the category.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->can('categories.view');
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->can('categories.create');
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->can('categories.update');
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->can('categories.delete');
    }
}
