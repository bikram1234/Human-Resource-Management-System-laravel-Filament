<?php

namespace App\Filament\Resources\LoanAdvanceApprovalRuleResource\Pages;

use App\Filament\Resources\LoanAdvanceApprovalRuleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoanAdvanceApprovalRules extends ListRecords
{
    protected static string $resource = LoanAdvanceApprovalRuleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
