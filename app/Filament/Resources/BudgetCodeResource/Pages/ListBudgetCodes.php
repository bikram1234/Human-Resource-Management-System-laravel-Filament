<?php

namespace App\Filament\Resources\BudgetCodeResource\Pages;

use App\Filament\Resources\BudgetCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBudgetCodes extends ListRecords
{
    protected static string $resource = BudgetCodeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
