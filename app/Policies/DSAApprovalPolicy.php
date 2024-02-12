<?php

namespace App\Policies;
use App\Models\DSAApproval;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;

class DSAApprovalPolicy
{
      /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("DSAApproval.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DSAApproval $DSAApproval): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("DSAApproval.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('DSAApproval.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DSAApproval $DSAApproval): bool
    {
        return $user->can('DSAApproval.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DSAApproval $DSAApproval): bool
    {
        return $user->can('DSAApproval.delete');
    }
}