<?php

namespace App\Policies;

use App\Models\ApplyAdvance;
use Chiiya\FilamentAccessControl\Models\FilamentUser as User;
use App\Models\ApplyAdvancet;
use Illuminate\Database\Eloquent\Model;
class ApplyAdvancePolicy
{
    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->status === Status::Draft;
    }
        public function viewSectionHead(User $user, ApplyAdvance $leave)
        {
            return $user->designation_id === $leave->user->section->head_designation_id;
        }
    
        public function viewDepartmentHead(User $user, ApplyAdvance $leave)
        {
            return $user->designation_id === $leave->user->department->head_designation_id;
        }
    
        public function viewManagement(User $user)
        {
            return $user->designation_id === 3; // Replace 3 with the actual designation_id for Management
        }
}
