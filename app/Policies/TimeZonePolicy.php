<?php

namespace App\Policies;

use App\Models\TimeZone;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;

class TimeZonePolicy
{
                  /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("TimeZone.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TimeZone $storelocation): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("TimeZone.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('TimeZone.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TimeZone $TimeZone): bool
    {
        return $user->can('TimeZone.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TimeZone $TimeZone): bool
    {
        return $user->can('TimeZone.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TimeZone $TimeZone): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TimeZone $TimeZone): bool
    {
        //
    }
}
