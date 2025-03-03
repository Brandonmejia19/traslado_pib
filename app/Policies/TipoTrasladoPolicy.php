<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\TipoTraslado;
use App\Models\User;

class TipoTrasladoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any TipoTraslado');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TipoTraslado $tipotraslado): bool
    {
        return $user->checkPermissionTo('view TipoTraslado');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create TipoTraslado');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TipoTraslado $tipotraslado): bool
    {
        return $user->checkPermissionTo('update TipoTraslado');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TipoTraslado $tipotraslado): bool
    {
        return $user->checkPermissionTo('delete TipoTraslado');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any TipoTraslado');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TipoTraslado $tipotraslado): bool
    {
        return $user->checkPermissionTo('restore TipoTraslado');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any TipoTraslado');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, TipoTraslado $tipotraslado): bool
    {
        return $user->checkPermissionTo('replicate TipoTraslado');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder TipoTraslado');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TipoTraslado $tipotraslado): bool
    {
        return $user->checkPermissionTo('force-delete TipoTraslado');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any TipoTraslado');
    }
}
