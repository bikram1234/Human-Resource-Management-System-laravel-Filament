<?php

namespace App\Policies;

use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
use App\Models\TransferClaimApproval;

class TransferClaimApprovalPolicy
{
     /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("TransferClaimApproval.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TransferClaimApproval $TransferClaimApproval): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("TransferClaimApproval.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('TransferClaimApproval.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TransferClaimApproval $TransferClaimApproval): bool
    {
        return $user->can('TransferClaimApproval.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TransferClaimApproval $TransferClaimApproval): bool
    {
        return $user->can('TransferClaimApproval.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TransferClaimApproval $TransferClaimApproval): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TransferClaimApproval $TransferClaimApproval): bool
    {
        //
    }
}
