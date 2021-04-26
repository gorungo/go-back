<?php

namespace App\Policies;

use App\User;
use App\Place;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlacePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the place.
     *
     * @param  \App\User  $user
     * @param  \App\Place  $place
     * @return mixed
     */
    public function list(User $user)
    {
        return true;
        return $user->hasPermissionTo('view places');
    }

    /**
     * Determine whether the user can view the place.
     *
     * @param  \App\User  $user
     * @param  \App\Place  $place
     * @return mixed
     */
    public function view(User $user, Place $place)
    {
        return true;
        return $user->hasPermissionTo('view places');
    }

    /**
     * Determine whether the user can create places.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('edit places');
    }

    /**
     * Determine whether the user can update the place.
     *
     * @param  \App\User  $user
     * @param  \App\Place  $place
     * @return mixed
     */
    public function update(User $user, Place $place)
    {
        // if can edit all ideas
        if($user->hasPermissionTo('edit places')){
            return true;
        }

        // if can edit own idea
        if($user->hasPermissionTo('edit own places')){
            //return $place->author_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the place.
     *
     * @param  \App\User  $user
     * @param  \App\Place  $place
     * @return mixed
     */
    public function delete(User $user, Place $place)
    {
        //
    }

    /**
     * Determine whether the user can restore the place.
     *
     * @param  \App\User  $user
     * @param  \App\Place  $place
     * @return mixed
     */
    public function restore(User $user, Place $place)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the place.
     *
     * @param  \App\User  $user
     * @param  \App\Place  $place
     * @return mixed
     */
    public function forceDelete(User $user, Place $place)
    {
        //
    }
}
