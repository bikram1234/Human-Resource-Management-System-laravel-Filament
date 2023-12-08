<?php

namespace App\Policies;

use App\Models\storelocation;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;

class storelocationPolicy
{
               /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("storelocation.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, storelocation $storelocation): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("storelocation.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('storelocation.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, storelocation $storelocation): bool
    {
        return $user->can('storelocation.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, storelocation $storelocation): bool
    {
        return $user->can('storelocation.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, storelocation $storelocation): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, storelocation $storelocation): bool
    {
        //
    }
}
