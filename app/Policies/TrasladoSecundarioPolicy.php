<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\TrasladoSecundario;
use App\Models\User;

class TrasladoSecundarioPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any TrasladoSecundario');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrasladoSecundario $trasladosecundario): bool
    {
        return $user->checkPermissionTo('view TrasladoSecundario');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create TrasladoSecundario');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrasladoSecundario $trasladosecundario): bool
    {
        return $user->checkPermissionTo('update TrasladoSecundario');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrasladoSecundario $trasladosecundario): bool
    {
        return $user->checkPermissionTo('delete TrasladoSecundario');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any TrasladoSecundario');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrasladoSecundario $trasladosecundario): bool
    {
        return $user->checkPermissionTo('restore TrasladoSecundario');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any TrasladoSecundario');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, TrasladoSecundario $trasladosecundario): bool
    {
        return $user->checkPermissionTo('replicate TrasladoSecundario');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder TrasladoSecundario');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrasladoSecundario $trasladosecundario): bool
    {
        return $user->checkPermissionTo('force-delete TrasladoSecundario');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any TrasladoSecundario');
    }
}
