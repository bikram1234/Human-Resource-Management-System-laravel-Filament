<?php

namespace App\Filament\Resources\LoanAdvanceApprovalRuleResource\Pages;

use App\Filament\Resources\LoanAdvanceApprovalRuleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoanAdvanceApprovalRule extends EditRecord
{
    protected static string $resource = LoanAdvanceApprovalRuleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
