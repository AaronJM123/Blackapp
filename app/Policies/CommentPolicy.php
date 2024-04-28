<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Comment $comment): Response|bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @return Response|bool
     */
    public function create(User $user)
    {
        //
    }

    public function update(User $user, Comment $comment): Response|bool
    {
        return $user->is($comment->author) && $user->tokenCan('comment:update');
    }

    public function delete(User $user, Comment $comment): Response|bool
    {
        return $user->is($comment->author) && $user->tokenCan('comment:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return Response|bool
     */
    public function restore(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return Response|bool
     */
    public function forceDelete(User $user, Comment $comment)
    {
        //
    }
}
