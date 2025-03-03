<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\UnidadListado;
use App\Models\User;

class UnidadListadoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any UnidadListado');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UnidadListado $unidadlistado): bool
    {
        return $user->checkPermissionTo('view UnidadListado');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create UnidadListado');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UnidadListado $unidadlistado): bool
    {
        return $user->checkPermissionTo('update UnidadListado');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UnidadListado $unidadlistado): bool
    {
        return $user->checkPermissionTo('delete UnidadListado');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any UnidadListado');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UnidadListado $unidadlistado): bool
    {
        return $user->checkPermissionTo('restore UnidadListado');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any UnidadListado');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, UnidadListado $unidadlistado): bool
    {
        return $user->checkPermissionTo('replicate UnidadListado');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder UnidadListado');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UnidadListado $unidadlistado): bool
    {
        return $user->checkPermissionTo('force-delete UnidadListado');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any UnidadListado');
    }
}
