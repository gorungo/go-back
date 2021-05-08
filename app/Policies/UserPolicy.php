<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return mixed
     */
    public function viewProfile(User $user, User $model)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('edit profiles', 'api');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return bool
     */
    public function updateProfile(User $user, User $model)
    {
        // if can edit all profile
        if($user->hasPermissionTo('edit profiles', 'api')){
            return true;
        }

        // if can edit own profile
        if($user->hasPermissionTo('edit profiles', 'api')){
            return $model->id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return bool
     */
    public function update(User $user, User $model)
    {
        // if can edit all users
        if($user->hasPermissionTo('edit profiles', 'api')){
            return true;
        }

        return $model->id === $user->id;

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
