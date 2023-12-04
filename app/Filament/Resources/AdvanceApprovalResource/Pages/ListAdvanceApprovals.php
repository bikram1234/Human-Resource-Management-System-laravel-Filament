<?php

namespace App\Filament\Resources\AdvanceApprovalResource\Pages;

use App\Filament\Resources\AdvanceApprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;


class ListAdvanceApprovals extends ListRecords
{
    protected static string $resource = AdvanceApprovalResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getTableQuery(): Builder
    {

    $query = parent::getTableQuery();
    $user = auth()->user();
   
    if ($user->is_sectionHead) {
        $sectionHeadId = $user->section_id;
        $query->whereHas('AdvanceApply.user', function ($subQuery) use ($sectionHeadId) {
            $subQuery->where('section_id', $sectionHeadId);
        })->where('level1', 'pending');
    }
    elseif ($user->is_departmentHead) {
        $departmentHeadId = $user->department_id;
        $query->whereHas('AdvanceApply.user', function ($subQuery) use ($departmentHeadId) {
            $subQuery->where('department_id', $departmentHeadId);
        })->whereHas('AdvanceApply', function($subQuery){
            $subQuery->where('status', 'pending');
        })->where('level1', 'approved')->where('level2', 'pending');
    } else {
        
        $query->where('level3', 'pending')->where('level2', 'approved');
    }

    return $query;
    }
    
}
