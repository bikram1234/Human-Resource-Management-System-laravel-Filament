<?php

namespace App\Policies;
use App\Models\AdvanceApproval;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;

class AdvanceApprovalPolicy
{
       /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("AdvanceApproval.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdvanceApproval $AdvanceApproval): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("AdvanceApproval.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('AdvanceApproval.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdvanceApproval $AdvanceApproval): bool
    {
        return $user->can('AdvanceApproval.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdvanceApproval $AdvanceApproval): bool
    {
        return $user->can('AdvanceApproval.delete');
    }
}
