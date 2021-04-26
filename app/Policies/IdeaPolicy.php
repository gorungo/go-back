<?php

namespace App\Policies;

use App\User;
use App\Idea;
use Illuminate\Auth\Access\HandlesAuthorization;

class IdeaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the idea.
     *
     * @param  \App\User  $user
     * @param  \App\Idea  $idea
     * @return mixed
     */
    public function view(?User $user, Idea $idea)
    {
        if($user){
            if($user->hasPermissionTo('view ideas')){
                // can see all published not blocked
                if(!$idea->isBlocked && $idea->isPublished){
                    return true;
                }

                // can see all published and own unpublished or blocked
                // can see all
                return $this->viewUnpublished($user, $idea);
            }
        }else{
            // can see all published not blocked
            if(!$idea->isBlocked && $idea->isPublished){
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can view the unpublished action.
     *
     * @param  \App\User  $user
     * @param  \App\Idea  $idea
     * @return mixed
     */
    public function viewUnpublished(User $user, Idea $idea)
    {
        // if can view all unpublished actions
        if($user->hasPermissionTo('view unpublished ideas')){
            return true;
        }

        // if can view own action
        if($user->hasPermissionTo('view own unpublished ideas')){
            return $idea->author_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create ideas.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('edit own ideas');
    }

    /**
     * Determine whether the user can create main ideas.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function createMainIdea(User $user)
    {
        return $user->hasPermissionTo('edit ideas');
    }

    /**
     * Determine whether the user can update the idea.
     *
     * @param  \App\User  $user
     * @param  \App\Idea  $idea
     * @return mixed
     */
    public function update(User $user, Idea $idea)
    {

        // if can edit all ideas
        if($user->hasPermissionTo('edit ideas')){
            return true;
        }

        // if can edit own idea
        if($user->hasPermissionTo('edit own ideas')){
            return $idea->author_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the idea.
     *
     * @param  \App\User  $user
     * @param  \App\Idea  $idea
     * @return mixed
     */
    public function delete(User $user, Idea $idea)
    {
        return $user->hasPermissionTo('delete ideas');
    }

    /**
     * Determine whether the user can restore the idea.
     *
     * @param  \App\User  $user
     * @param  \App\Idea  $idea
     * @return mixed
     */
    public function restore(User $user, Idea $idea)
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Determine whether the user can permanently delete the idea.
     *
     * @param  \App\User  $user
     * @param  \App\Idea  $idea
     * @return mixed
     */
    public function forceDelete(User $user, Idea $idea)
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
