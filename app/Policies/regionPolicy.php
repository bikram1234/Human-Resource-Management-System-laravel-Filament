<?php

namespace App\Policies;

use App\Models\region;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
class regionPolicy
{
                 /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("region.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, region $region): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("region.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('region.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, region $region): bool
    {
        return $user->can('region.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, region $region): bool
    {
        return $user->can('region.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, region $region): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, region $region): bool
    {
        //
    }
}
