<?php

namespace App\Policies;

use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
use App\Models\ExpenseApproval;

class ExpenseApprovalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("ExpenseApproval.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExpenseApproval $ExpenseApproval): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("ExpenseApproval.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('ExpenseApproval.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExpenseApproval $ExpenseApproval): bool
    {
        return $user->can('ExpenseApproval.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExpenseApproval $ExpenseApproval): bool
    {
        return $user->can('ExpenseApproval.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExpenseApproval $ExpenseApproval): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExpenseApproval $ExpenseApproval): bool
    {
        //
    }
}
