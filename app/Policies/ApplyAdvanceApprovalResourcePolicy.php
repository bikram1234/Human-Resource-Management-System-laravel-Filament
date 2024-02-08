<?php

namespace App\Policies;

use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
use App\Filament\Resources\ApplyAdvanceApprovalResource as approval;



class ApplyAdvanceApprovalResourcePolicy
{
     /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("approval.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, approval $approval): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("approval.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('approval.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, approval $approval): bool
    {
        return $user->can('approval.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, approval $approval): bool
    {
        return $user->can('approval.delete');
    }
}
