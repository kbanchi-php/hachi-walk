<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Walk;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class WalkPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(?User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Walk $walk)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Walk $walk)
    {
        return $user->id === $walk->user_id
            ? Response::allow()
            : Response::deny('You are not allowed this action.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Walk $walk)
    {
        return $user->id === $walk->user_id
            ? Response::allow()
            : Response::deny('You are not allowed this action.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Walk $walk)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Walk $walk)
    {
        //
    }
}
