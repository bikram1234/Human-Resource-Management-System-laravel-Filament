<?php

namespace App\Filament\Resources\ExpenseApprovalRuleResource\Pages;

use App\Filament\Resources\ExpenseApprovalRuleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpenseApprovalRule extends EditRecord
{
    protected static string $resource = ExpenseApprovalRuleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
