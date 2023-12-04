<?php

namespace App\Policies;

use App\Models\LeavePolicy;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
class LeavePolicyPolicy
{
             /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("LeavePolicy.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeavePolicy $LeavePolicy): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("LeavePolicy.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('LeavePolicy.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeavePolicy $LeavePolicy): bool
    {
        return $user->can('LeavePolicy.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeavePolicy $LeavePolicy): bool
    {
        return $user->can('LeavePolicy.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LeavePolicy $LeavePolicy): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LeavePolicy $LeavePolicy): bool
    {
        //
    }
}
