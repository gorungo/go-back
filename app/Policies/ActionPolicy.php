<?php

namespace App\Policies;

use App\User;
use App\Action;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActionPolicy
{
    use HandlesAuthorization;



    /**
     * Determine whether the user can view the action.
     *
     * @param  \App\User  $user
     * @param  \App\Action  $action
     * @return mixed
     */
    public function view(User $user, Action $action)
    {

        if($action->isPublished) {
            return $user->hasPermissionTo('view actions');
        }

        return false;
    }

    /**
     * Determine whether the user can view the unpublished action.
     *
     * @param  \App\User  $user
     * @param  \App\Action  $action
     * @return mixed
     */
    public function viewUnpublished(User $user, Action $action)
    {
        if( ! $action->isPublished){
            // if can view all unpublished actions
            if($user->hasPermissionTo('view unpublished actions')){
                return true;
            }

            // if can view own action
            if($user->hasPermissionTo('view own unpublished actions')){
                return $action->author_id === $user->id;
            }

            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can create actions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAnyPermission(['edit own actions', 'edit actions']);
    }

    /**
     * Determine whether the user can update the action.
     *
     * @param  \App\User  $user
     * @param  \App\Action  $action
     * @return mixed
     */
    public function update(User $user, Action $action)
    {
        // if can edit all ideas
        if($user->hasPermissionTo('edit actions')){
            return true;
        }

        // if can edit own idea
        if($user->hasPermissionTo('edit own actions')){
            return $action->author_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the action.
     *
     * @param  \App\User  $user
     * @param  \App\Action  $action
     * @return mixed
     */
    public function delete(User $user, Action $action)
    {
        // if can delete all ideas
        if($user->hasAnyRole(['admin', 'super-admin'])){
            return true;
        }

        // if can edit own idea
        if($user->hasPermissionTo('delete own actions')){
            return $action->author_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the action.
     *
     * @param  \App\User  $user
     * @param  \App\Action  $action
     * @return mixed
     */
    public function restore(User $user, Action $action)
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Determine whether the user can permanently delete the action.
     *
     * @param  \App\User  $user
     * @param  \App\Action  $action
     * @return mixed
     */
    public function forceDelete(User $user, Action $action)
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
