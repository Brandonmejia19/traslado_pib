<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PrivadoListado;
use App\Models\User;

class PrivadoListadoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PrivadoListado');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PrivadoListado $privadolistado): bool
    {
        return $user->checkPermissionTo('view PrivadoListado');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PrivadoListado');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PrivadoListado $privadolistado): bool
    {
        return $user->checkPermissionTo('update PrivadoListado');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PrivadoListado $privadolistado): bool
    {
        return $user->checkPermissionTo('delete PrivadoListado');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any PrivadoListado');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PrivadoListado $privadolistado): bool
    {
        return $user->checkPermissionTo('restore PrivadoListado');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any PrivadoListado');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PrivadoListado $privadolistado): bool
    {
        return $user->checkPermissionTo('replicate PrivadoListado');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder PrivadoListado');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PrivadoListado $privadolistado): bool
    {
        return $user->checkPermissionTo('force-delete PrivadoListado');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any PrivadoListado');
    }
}
