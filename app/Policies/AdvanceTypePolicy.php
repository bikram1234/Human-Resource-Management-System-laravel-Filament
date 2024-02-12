<?php

namespace App\Policies;

use App\Models\AdvanceType;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;

class AdvanceTypePolicy
{
            /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("AdvanceType.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdvanceType $AdvanceType): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("AdvanceType.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('AdvanceType.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdvanceType $AdvanceType): bool
    {
        return $user->can('AdvanceType.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdvanceType $AdvanceType): bool
    {
        return $user->can('AdvanceType.delete');
    }

}