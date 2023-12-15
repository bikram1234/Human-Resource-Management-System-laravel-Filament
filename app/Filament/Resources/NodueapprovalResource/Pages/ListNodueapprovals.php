<?php

namespace App\Filament\Resources\NodueapprovalResource\Pages;

use App\Filament\Resources\NodueapprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListNodueapprovals extends ListRecords
{
    protected static string $resource = NodueapprovalResource::class;

    protected function getActions(): array
    {
        return [
        ];
    }

    protected function getTableQuery(): Builder
    {

    $query = parent::getTableQuery();
    $user = auth()->user();
   
    if ($user->hasRole('Section Head')) {
        $sectionHeadId = $user->section_id;
        $query->whereHas('nodue.user', function ($subQuery) use ($sectionHeadId) {
            $subQuery->where('section_id', $sectionHeadId);
        })->where('status1', 'pending');
    }
    elseif ($user->hasRole('Department Head')) {
        $departmentHeadId = $user->department_id;
        $query->whereHas('nodue.user', function ($subQuery) use ($departmentHeadId) {
        })->whereHas('nodue', function($subQuery){
            $subQuery->where('status', 'pending');
        })->where('status1', 'approved')->where('status2', 'pending');
    } else {
        
        $query->where('status1', 'approved')->where('status2', 'approved');
    }

    return $query;
    }
    
}
