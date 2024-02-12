<?php

namespace App\Policies;

use App\Models\LeaveType;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
class LeaveTypePolicy
{
           /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("LeaveType.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveType $LeaveType): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("LeaveType.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('LeaveType.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveType $LeaveType): bool
    {
        return $user->can('LeaveType.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveType $LeaveType): bool
    {
        return $user->can('LeaveType.delete');
    }
}