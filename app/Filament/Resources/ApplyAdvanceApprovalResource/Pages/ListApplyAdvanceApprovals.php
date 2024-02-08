<?php

namespace App\Filament\Resources\ApplyAdvanceApprovalResource\Pages;

use App\Filament\Resources\ApplyAdvanceApprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ApplyAdvance;



class ListApplyAdvanceApprovals extends ListRecords
{
    protected static string $resource = ApplyAdvanceApprovalResource::class;

    protected function getTableQuery(): Builder
    {
        
    $query = parent::getTableQuery();
    $user = auth()->user();
   
    if ($user->hasRole('Section Head')) {
        $sectionHeadId = $user->section_id;
        $query->whereHas('user', function ($subQuery) use ($sectionHeadId) {
            $subQuery->where('section_id', $sectionHeadId);
        })->where('level1', 'pending')->where('status', 'pending');
    }
    elseif ($user->hasRole('Department Head')) {
        $departmentHeadId = $user->department_id;
        $query->whereHas('user', function ($subQuery) use ($departmentHeadId) {
            $subQuery->where('department_id', $departmentHeadId);
        })->where('level2', 'pending')->where('level3', 'pending')->where('status','pending')->where('level1','approved');
    } else {
        
        $query->where('level3', 'pending')->where('level2', 'approved');
    }

    return $query;
    }
    
}
