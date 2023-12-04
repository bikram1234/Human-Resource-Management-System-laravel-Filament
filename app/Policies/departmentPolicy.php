<?php

namespace App\Policies;

use App\Models\department;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
class departmentPolicy
{
             /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("department.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, department $department): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("department.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('department.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, department $department): bool
    {
        return $user->can('department.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, department $department): bool
    {
        return $user->can('department.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, department $department): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, department $department): bool
    {
        //
    }
}
