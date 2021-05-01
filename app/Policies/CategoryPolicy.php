<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the category.
     *
     * @param  User  $user
     * @param  Category  $category
     * @return bool
     */
    public function view(User $user, Category $category)
    {
        return $user->hasPermissionTo('view categories');
    }

    /**
     * Determine whether the user can create categories.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('edit categories');
    }

    /**
     * Determine whether the user can update the category.
     *
     * @param  User  $user
     * @param  Category  $category
     * @return bool
     */
    public function update(User $user, Category $category)
    {
        return $user->hasPermissionTo('edit categories');
    }

    /**
     * Determine whether the user can delete the category.
     *
     * @param  User  $user
     * @param  Category  $category
     * @return bool
     */
    public function delete(User $user, Category $category)
    {
        return $user->hasPermissionTo('delete categories');
    }

    /**
     * Determine whether the user can restore the category.
     *
     * @param  User  $user
     * @param  Category  $category
     * @return bool
     */
    public function restore(User $user, Category $category)
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Determine whether the user can permanently delete the category.
     *
     * @param  User  $user
     * @param  Category  $category
     * @return bool
     */
    public function forceDelete(User $user, Category $category)
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
