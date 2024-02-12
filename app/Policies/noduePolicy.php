<?php

namespace App\Policies;

use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
use App\Models\nodue;
use Illuminate\Database\Eloquent\Model;

class noduePolicy
{
    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === Status::Draft;
    }
        public function viewSectionHead(User $user, nodue $nodue)
        {
            return $user->designation_id === $nodue->user->section->head_designation_id;
        }
    
        // public function viewDepartmentHead(User $user, nodue $nodue)
        // {
        //     return $user->designation_id === $nodue->user->department->head_designation_id;
        // }
    
        public function viewManagement(User $user)
        {
            return $user->designation_id === 3; // Replace 3 with the actual designation_id for Management
        }
}