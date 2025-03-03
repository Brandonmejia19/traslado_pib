<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\TrasladoSecundarioPropios;
use App\Models\User;

class TrasladoSecundarioPropiosPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any TrasladoSecundarioPropios');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrasladoSecundarioPropios $trasladosecundariopropios): bool
    {
        return $user->checkPermissionTo('view TrasladoSecundarioPropios');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create TrasladoSecundarioPropios');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrasladoSecundarioPropios $trasladosecundariopropios): bool
    {
        return $user->checkPermissionTo('update TrasladoSecundarioPropios');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrasladoSecundarioPropios $trasladosecundariopropios): bool
    {
        return $user->checkPermissionTo('delete TrasladoSecundarioPropios');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any TrasladoSecundarioPropios');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrasladoSecundarioPropios $trasladosecundariopropios): bool
    {
        return $user->checkPermissionTo('restore TrasladoSecundarioPropios');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any TrasladoSecundarioPropios');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, TrasladoSecundarioPropios $trasladosecundariopropios): bool
    {
        return $user->checkPermissionTo('replicate TrasladoSecundarioPropios');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder TrasladoSecundarioPropios');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrasladoSecundarioPropios $trasladosecundariopropios): bool
    {
        return $user->checkPermissionTo('force-delete TrasladoSecundarioPropios');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any TrasladoSecundarioPropios');
    }
}
