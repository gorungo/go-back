<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Place;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlacePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the place.
     *
     * @param  User  $user
     * @param  Place  $place
     * @return bool
     */
    public function list(User $user)
    {
        return true;
        //return $user->hasPermissionTo('view places');
    }

    /**
     * Determine whether the user can view the place.
     *
     * @param  User  $user
     * @param  Place  $place
     * @return bool
     */
    public function view(User $user, Place $place)
    {
        return true;
       // return $user->hasPermissionTo('view places');
    }

    /**
     * Determine whether the user can create places.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('edit places', 'api');
    }

    /**
     * Determine whether the user can update the place.
     *
     * @param  User  $user
     * @param  Place  $place
     * @return bool
     */
    public function update(User $user, Place $place)
    {
        // if can edit all ideas
        if($user->hasPermissionTo('edit places', 'api')){
            return true;
        }

        // if can edit own idea
        if($user->hasPermissionTo('edit own places', 'api')){
            //return $place->author_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the place.
     *
     * @param  User  $user
     * @param  Place  $place
     * @return void
     */
    public function delete(User $user, Place $place)
    {
        //
    }

    /**
     * Determine whether the user can restore the place.
     *
     * @param  User  $user
     * @param  Place  $place
     * @return void
     */
    public function restore(User $user, Place $place)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the place.
     *
     * @param  User  $user
     * @param  Place  $place
     * @return void
     */
    public function forceDelete(User $user, Place $place)
    {
        //
    }
}
