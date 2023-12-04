<?php

namespace App\Filament\Resources\ExpenseApprovalResource\Pages;

use App\Filament\Resources\ExpenseApprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpenseApproval extends EditRecord
{
    protected static string $resource = ExpenseApprovalResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
