<?php

namespace App\Policies;

use App\Models\HolidayType;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;

class HolidayTypePolicy
{
              /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("HolidayType.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HolidayType $HolidayType): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("HolidayType.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('HolidayType.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HolidayType $HolidayType): bool
    {
        return $user->can('HolidayType.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HolidayType $HolidayType): bool
    {
        return $user->can('HolidayType.delete');
    }
}