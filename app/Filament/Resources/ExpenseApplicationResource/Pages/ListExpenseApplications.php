<?php

namespace App\Filament\Resources\ExpenseApplicationResource\Pages;

use App\Filament\Resources\ExpenseApplicationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListExpenseApplications extends ListRecords
{
    protected static string $resource = ExpenseApplicationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
     $query = parent::getTableQuery()->whereuser_id(Auth::id());
     return $query;
    }
}
