<?php

namespace App\Policies;

use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
use App\Models\FuelApproval;

class FuelApprovalPolicy
{
     /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("FuelApproval.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FuelApproval $FuelApproval): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("FuelApproval.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('FuelApproval.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FuelApproval $FuelApproval): bool
    {
        return $user->can('FuelApproval.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FuelApproval $FuelApproval): bool
    {
        return $user->can('FuelApproval.delete');
    }
}