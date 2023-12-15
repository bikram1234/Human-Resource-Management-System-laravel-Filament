<?php

namespace App\Filament\Resources\ExpenseApprovalResource\Pages;

use App\Filament\Resources\ExpenseApprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;


class ListExpenseApprovals extends ListRecords
{
    protected static string $resource = ExpenseApprovalResource::class;

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
   
    if ($user->hasRole('Section Head')) {
        $sectionHeadId = $user->section_id;
        $query->whereHas('ExpenseApply.user', function ($subQuery) use ($sectionHeadId) {
            $subQuery->where('section_id', $sectionHeadId);
        })->where('level1', 'pending');
    }
    elseif ($user->hasRole('Department Head')) {
        $departmentHeadId = $user->department_id;
        $query->whereHas('ExpenseApply.user', function ($subQuery) use ($departmentHeadId) {
            $subQuery->where('department_id', $departmentHeadId);
        })->whereHas('ExpenseApply', function($subQuery){
            $subQuery->where('status', 'pending');
        })->where('level1', 'approved')->where('level2', 'pending');
    } else {
        
        $query->where('level3', 'pending')->where('level2', 'approved');
    }

    return $query;
    }
}
