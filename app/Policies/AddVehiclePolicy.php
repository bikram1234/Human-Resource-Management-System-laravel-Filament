<?php

namespace App\Policies;

use App\Models\AddVehicle;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;

class AddVehiclePolicy
{
                /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("AddVehicle.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AddVehicle $AddVehicle): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("AddVehicle.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('AddVehicle.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AddVehicle $AddVehicle): bool
    {
        return $user->can('AddVehicle.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AddVehicle $AddVehicle): bool
    {
        return $user->can('AddVehicle.delete');
    }

}
