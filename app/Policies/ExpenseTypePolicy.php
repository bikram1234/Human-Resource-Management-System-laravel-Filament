<?php

namespace App\Policies;
use App\Models\ExpenseType;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;

class ExpenseTypePolicy
{
        /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("ExpenseType.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExpenseType $ExpenseType): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("ExpenseType.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('ExpenseType.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExpenseType $ExpenseType): bool
    {
        return $user->can('ExpenseType.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExpenseType $ExpenseType): bool
    {
        return $user->can('ExpenseType.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExpenseType $ExpenseType): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExpenseType $ExpenseType): bool
    {
        //
    }
}
