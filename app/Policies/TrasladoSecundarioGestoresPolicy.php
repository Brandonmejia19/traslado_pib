<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\TrasladoSecundarioGestores;
use App\Models\User;

class TrasladoSecundarioGestoresPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any TrasladoSecundarioGestores');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrasladoSecundarioGestores $trasladosecundariogestores): bool
    {
        return $user->checkPermissionTo('view TrasladoSecundarioGestores');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create TrasladoSecundarioGestores');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrasladoSecundarioGestores $trasladosecundariogestores): bool
    {
        return $user->checkPermissionTo('update TrasladoSecundarioGestores');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrasladoSecundarioGestores $trasladosecundariogestores): bool
    {
        return $user->checkPermissionTo('delete TrasladoSecundarioGestores');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any TrasladoSecundarioGestores');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrasladoSecundarioGestores $trasladosecundariogestores): bool
    {
        return $user->checkPermissionTo('restore TrasladoSecundarioGestores');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any TrasladoSecundarioGestores');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, TrasladoSecundarioGestores $trasladosecundariogestores): bool
    {
        return $user->checkPermissionTo('replicate TrasladoSecundarioGestores');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder TrasladoSecundarioGestores');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrasladoSecundarioGestores $trasladosecundariogestores): bool
    {
        return $user->checkPermissionTo('force-delete TrasladoSecundarioGestores');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any TrasladoSecundarioGestores');
    }
}
