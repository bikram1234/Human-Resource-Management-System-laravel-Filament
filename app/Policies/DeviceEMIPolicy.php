<?php

namespace App\Policies;

use App\Models\DeviceEMI;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;

class DeviceEMIPolicy
{
               /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("DeviceEMI.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeviceEMI $DeviceEMI): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("DeviceEMI.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('DeviceEMI.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeviceEMI $DeviceEMI): bool
    {
        return $user->can('DeviceEMI.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeviceEMI $DeviceEMI): bool
    {
        return $user->can('DeviceEMI.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
   
}
