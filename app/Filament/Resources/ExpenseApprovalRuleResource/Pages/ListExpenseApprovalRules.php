<?php

namespace App\Filament\Resources\ExpenseApprovalRuleResource\Pages;

use App\Filament\Resources\ExpenseApprovalRuleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpenseApprovalRules extends ListRecords
{
    protected static string $resource = ExpenseApprovalRuleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
