<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Idea;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class IdeaPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the idea.
     *
     * @param  User|null  $user
     * @param  Idea  $idea
     * @return mixed
     */
    public function view(?User $user, Idea $idea)
    {
        return true;

        if(request()->has('edit')){
            if($user){
                if($user->hasPermissionTo('edit own ideas', 'api')){
                    return $idea->author_id === $user->id;
                }
            }
        }

        // can see all published not blocked
        if(!$idea->isBlocked && $idea->isPublished){
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the unpublished action.
     *
     * @param  User  $user
     * @param  Idea  $idea
     * @return bool
     */
    public function viewUnpublished(User $user, Idea $idea)
    {
        // if can view all unpublished actions
        if($user->hasPermissionTo('view unpublished ideas', 'api')){
            return true;
        }

        // if can view own action
        if($user->hasPermissionTo('view own unpublished ideas', 'api')){
            return $idea->author_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create ideas.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('edit own ideas', 'api');
    }

    /**
     * Determine whether the user can create main ideas.
     *
     * @param  User  $user
     * @return bool
     */
    public function createMainIdea(User $user)
    {
        return $user->hasPermissionTo('edit ideas', 'api');
    }

    /**
     * Determine whether the user can update the idea.
     *
     * @param  User  $user
     * @param  Idea  $idea
     * @return bool
     */
    public function edit(User $user, Idea $idea)
    {
        // if can edit all ideas
        if($user->hasPermissionTo('edit ideas', 'api')){
            return true;
        }

        // if can edit own idea
        if($user->hasPermissionTo('edit own ideas', 'api')){
            return $idea->author_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the idea.
     *
     * @param  User  $user
     * @param  Idea  $idea
     * @return bool
     */
    public function update(User $user, Idea $idea)
    {
        // if can edit all ideas
        if($user->hasPermissionTo('edit ideas', 'api')){
            return true;
        }

        // if can edit own idea
        if($user->hasPermissionTo('edit own ideas', 'api')){
            return $idea->author_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the idea.
     *
     * @param  User  $user
     * @param  Idea  $idea
     * @return bool
     */
    public function delete(User $user, Idea $idea)
    {
        return $idea->author_id === $user->id;
        return $user->hasPermissionTo('delete ideas', 'api') || $user->hasPermissionTo('delete own ideas', 'api');
    }

    /**
     * Determine whether the user can restore the idea.
     *
     * @param  User  $user
     * @param  Idea  $idea
     * @return bool
     */
    public function restore(User $user, Idea $idea)
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Determine whether the user can permanently delete the idea.
     *
     * @param  User  $user
     * @param  Idea  $idea
     * @return bool
     */
    public function forceDelete(User $user, Idea $idea)
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
