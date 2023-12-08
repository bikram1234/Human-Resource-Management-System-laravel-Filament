<?php

namespace App\Policies;

use App\Models\encashment;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
class encashmentPolicy
{
              /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("encashment.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, encashment $encashment): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("encashment.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('encashment.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, encashment $encashment): bool
    {
        return $user->can('encashment.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, encashment $encashment): bool
    {
        return $user->can('encashment.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, encashment $encashment): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, encashment $encashment): bool
    {
        //
    }
}
