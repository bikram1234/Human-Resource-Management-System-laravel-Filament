<?php

namespace App\Policies;

use App\Models\policy;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
class policyPolicy
{
         /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("policy.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, policy $policy): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("policy.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('policy.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, policy $policy): bool
    {
        return $user->can('policy.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, policy $policy): bool
    {
        return $user->can('policy.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, policy $policy): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, policy $policy): bool
    {
        //
    }
}
