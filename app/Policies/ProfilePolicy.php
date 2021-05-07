<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the profile.
     *
     * @param  User  $user
     * @param  Profile  $profile
     * @return mixed
     */
    public function view(User $user, Profile $profile)
    {
        if($user->hasPermissionTo('view own profiles', 'api')){
            return $profile->user_id === $user->id;
        }
    }

    /**
     * Determine whether the user can create profiles.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the profile.
     *
     * @param  User  $user
     * @param  Profile  $profile
     * @return bool
     */
    public function update(User $user, Profile $profile)
    {
        if($user->hasPermissionTo('edit own profiles', 'api')){
            return $profile->user_id === $user->id;
        }
    }

    /**
     * Determine whether the user can delete the profile.
     *
     * @param  User  $user
     * @param  Profile  $profile
     * @return void
     */
    public function delete(User $user, Profile $profile)
    {
        //
    }

    /**
     * Determine whether the user can restore the profile.
     *
     * @param  User  $user
     * @param  Profile  $profile
     * @return void
     */
    public function restore(User $user, Profile $profile)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the profile.
     *
     * @param  User  $user
     * @param  Profile  $profile
     * @return void
     */
    public function forceDelete(User $user, Profile $profile)
    {
        //
    }
}
