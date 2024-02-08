<?php

namespace App\Policies;

use App\Models\VehicleType;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;

class VehicleTypePolicy
{
                /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("VehicleType.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VehicleType $VehicleType): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("VehicleType.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('VehicleType.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VehicleType $VehicleType): bool
    {
        return $user->can('VehicleType.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VehicleType $VehicleType): bool
    {
        return $user->can('VehicleType.delete');
    }

}
