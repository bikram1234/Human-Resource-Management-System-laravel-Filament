<?php

namespace App\Policies;

use App\Models\EncashmentApproval;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
class EncashmentApprovalPolicy
{
      /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("EncashmentApproval.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EncashmentApproval $EncashmentApproval): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("EncashmentApproval.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('EncashmentApproval.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EncashmentApproval $EncashmentApproval): bool
    {
        return $user->can('EncashmentApproval.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EncashmentApproval $EncashmentApproval): bool
    {
        return $user->can('EncashmentApproval.delete');
    }

}
