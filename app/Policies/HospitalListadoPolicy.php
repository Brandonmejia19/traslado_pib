<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\HospitalListado;
use App\Models\User;

class HospitalListadoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any HospitalListado');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HospitalListado $hospitallistado): bool
    {
        return $user->checkPermissionTo('view HospitalListado');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create HospitalListado');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HospitalListado $hospitallistado): bool
    {
        return $user->checkPermissionTo('update HospitalListado');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HospitalListado $hospitallistado): bool
    {
        return $user->checkPermissionTo('delete HospitalListado');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any HospitalListado');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, HospitalListado $hospitallistado): bool
    {
        return $user->checkPermissionTo('restore HospitalListado');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any HospitalListado');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, HospitalListado $hospitallistado): bool
    {
        return $user->checkPermissionTo('replicate HospitalListado');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder HospitalListado');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, HospitalListado $hospitallistado): bool
    {
        return $user->checkPermissionTo('force-delete HospitalListado');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any HospitalListado');
    }
}
