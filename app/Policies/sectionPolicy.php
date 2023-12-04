<?php

namespace App\Policies;

use App\Models\section;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;

class sectionPolicy
{
                 /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("section.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, section $section): bool
    {
        // dd($LeaveApproval->AppliedLeave->employee_id);

            return $user->can("section.view");
        
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('section.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, section $section): bool
    {
        return $user->can('section.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, section $section): bool
    {
        return $user->can('section.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, section $section): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, section $section): bool
    {
        //
    }
}
