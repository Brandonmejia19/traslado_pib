<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\TrasladoSecundarioHistorico;
use App\Models\User;

class TrasladoSecundarioHistoricoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any TrasladoSecundarioHistorico');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrasladoSecundarioHistorico $trasladosecundariohistorico): bool
    {
        return $user->checkPermissionTo('view TrasladoSecundarioHistorico');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create TrasladoSecundarioHistorico');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrasladoSecundarioHistorico $trasladosecundariohistorico): bool
    {
        return $user->checkPermissionTo('update TrasladoSecundarioHistorico');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrasladoSecundarioHistorico $trasladosecundariohistorico): bool
    {
        return $user->checkPermissionTo('delete TrasladoSecundarioHistorico');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any TrasladoSecundarioHistorico');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrasladoSecundarioHistorico $trasladosecundariohistorico): bool
    {
        return $user->checkPermissionTo('restore TrasladoSecundarioHistorico');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any TrasladoSecundarioHistorico');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, TrasladoSecundarioHistorico $trasladosecundariohistorico): bool
    {
        return $user->checkPermissionTo('replicate TrasladoSecundarioHistorico');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder TrasladoSecundarioHistorico');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrasladoSecundarioHistorico $trasladosecundariohistorico): bool
    {
        return $user->checkPermissionTo('force-delete TrasladoSecundarioHistorico');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any TrasladoSecundarioHistorico');
    }
}
